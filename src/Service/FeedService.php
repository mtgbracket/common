<?php


namespace Mtgbracket\Service;


use Doctrine\DBAL\Driver\PDOStatement;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class FeedService
 * @package Mtgbracket\Service
 */
class FeedService
{
    /** @var ContainerInterface  */
    private $container;

    /**
     * FeedService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param int $userId
     * @param int $targetId
     * @param string $template
     * @param string $message
     * @param array $data
     * @param string|null $eventId
     * @throws \Exception
     */
    public function postEvent(int $userId, int $targetId, string $template, string $message, ?array $data = null, ?string $eventId = null)
    {
        /**
         * default
         */
        if($eventId == null) {
            $eventId = substr(bin2hex(random_bytes(32)), 0, 32);
        }

        $this->container->get('event_dispatcher')->dispatch(
            new GenericEvent([
                'user_id' => $userId,
                'target_id' => $targetId,
                'template' => 'event.organization_invite',
                'event_id' => $eventId,
                'message' => $message,
                'data' => $data,
            ]),
            'api.feed_event.dispatch'
        );
    }
}