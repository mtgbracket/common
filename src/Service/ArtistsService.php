<?php


namespace Mtgbracket\Service;


use Mtgbracket\Service\Abstraction\BaseMicroservice;

/**
 * Class ArtistsService
 * @package Mtgbracket\Service
 */
class ArtistsService extends BaseMicroservice
{
    /**
     * @return string
     */
    protected function getDefaultEndpoint(): string
    {
        return 'http://artists.mtgbracket.com';
    }

    /**
     * @return string
     */
    protected function getDefaultServiceName(): string
    {
        return 'artists';
    }

    /**
     * @param int $artistId
     * @return array|null
     */
    public function getArtist(int $artistId): ?array
    {
        return $this->request(sprintf("/artists/%d", $artistId), 'GET');
    }

    /**
     * @param string $credit
     * @param string|null $firstName
     * @param string|null $lastName
     * @return array|null
     */
    public function createArtist(string $credit, string $firstName = null, string $lastName = null): ?array
    {
        return $this->request("/artists", 'POST', json_encode([
            'credit' => $credit,
            'first_name' => $firstName,
            'last_name' => $lastName
        ]));
    }
}