<?php


namespace Mtgbracket\Service\Abstraction;


use MisfitPixel\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BaseMicroservice
 * @package Mtgbracket\Service\Abstraction
 */
abstract class BaseMicroservice
{
    /** @var ContainerInterface  */
    private $container;

    /** @var RequestStack  */
    private $request;

    /**
     * BaseMicroservice constructor.
     * @param ContainerInterface $container
     * @param RequestStack $request
     */
    public function __construct(ContainerInterface $container, RequestStack $request)
    {
        $this->container = $container;
        $this->request = $request;
    }

    /**
     * @return string
     */
    protected abstract function getDefaultEndpoint(): string;

    /**
     * @return string
     */
    protected abstract function getDefaultServiceName(): string;

    /**
     * @return string
     */
    private function getBaseUrl(): string
    {
        $endpoint = $this->getDefaultEndpoint();

        /**
         * set the service endpoint based on the Kubernetes service IP.
         */
        if(
            $this->getContainer()->hasParameter('microservices') &&
            $this->getContainer()->getParameter('microservices')[$this->getDefaultServiceName()] != null
        ) {
            $endpoint = $this->getContainer()->getParameter('microservices')[$this->getDefaultServiceName()];
        }

        return $endpoint;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @param string $path
     * @param string $method
     * @param string $body
     * @return array|null
     */
    protected function request(string $path, string $method, string $body = '', array $headers = []): ?array
    {
        $ch = curl_init();

        /**
         * pass through the oauth token.
         */
        if(empty($headers)) {
            $headers = [
                sprintf('Authorization: %s', $this->request->getCurrentRequest()->headers->get('Authorization'))
            ];
        }

        curl_setopt($ch, CURLOPT_URL, sprintf('%s/%s', $this->getBaseUrl(), $path));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        /**
         * optionally assign request body.
         */
        switch($method){
            case "POST":
            case "DELETE":
            case "PUT":
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                $headers = array_merge($headers, [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($body)
                ]);

                break;

            default:
                break;
        }

        /**
         * attach headers.
         */
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        /**
         * configure CURL request options.
         */
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        /**
         * execute request.
         */
        $result = (curl_exec($ch));
        $errorCode = curl_errno($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);

        /**
         * verify CURL response.
         */
        switch($errorCode) {
            case CURLE_OK:
                break;

            case CURLE_OPERATION_TIMEOUTED:
                throw new Exception\TimeoutException(sprintf('Could not connect to %s service', $this->getDefaultServiceName()));

            default:
                throw new Exception\UnknownErrorException('Error encountered during api request');
        }

        /**
         * verify service response.
         */
        switch($info['http_code']){
            case Response::HTTP_OK:
            case Response::HTTP_CREATED:
            case Response::HTTP_ACCEPTED:
            case Response::HTTP_NO_CONTENT:
                break;

            case Response::HTTP_NOT_FOUND:
                throw new Exception\EntityNotFoundException();

            case Response::HTTP_FORBIDDEN:
                throw new Exception\ForbiddenException();

            default:
                throw new Exception\UnknownErrorException();
        }

        return ($result != null) ? json_decode($result, true) : null;
    }
}