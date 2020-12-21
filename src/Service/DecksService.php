<?php


namespace Mtgbracket\Service;


use Mtgbracket\Service\Abstraction\BaseMicroservice;

/**
 * Class DecksService
 * @package Mtgbracket\Service
 */
class DecksService extends BaseMicroservice
{
    /**
     * @return string
     */
    protected function getDefaultEndpoint(): string
    {
        return 'http://decks.mtgbracket.com';
    }

    /**
     * @return string
     */
    protected function getDefaultServiceName(): string
    {
        return 'decks';
    }

    /**
     * @param string $identifier
     * @return array|null
     */
    public function getDeck(string $identifier): ?array
    {
        return $this->request(sprintf("/decks/%s", $identifier), 'GET');
    }

    /**
     * @param string $name
     * @param array $headers
     * @return array|null
     */
    public function createDeck(string $name, array $headers): ?array
    {
        return $this->request("/decks", 'POST', json_encode([
            'name' => $name
        ]), $headers);
    }

    /**
     * @param string $identifier
     * @param string|null $name
     * @param array|null $data
     * @param array|null $cards
     * @param array $headers
     * @return array|null
     */
    public function updateDeck(string $identifier, ?string $name, ?array $data, ?array $cards, array $headers): ?array
    {
        $request = [];

        if(null !== $name) {
            $request['name'] = $name;
        }

        if(null !== $data) {
            $request['data'] = $data;
        }

        if(null !== $cards) {
            $request['cards'] = $cards;
        }

        return $this->request(sprintf("/decks/%s", $identifier), 'PUT', json_encode($request), $headers);
    }

    /**
     * @param string $query
     * @return array|null
     */
    public function searchCards(string $query): ?array
    {
        return $this->request(sprintf("/cards?q=%s", $query), 'GET');
    }

    /**
     * @param string $identifier
     * @param int $eventId
     * @return array|null
     */
    public function validateDeck(string $identifier, int $eventId): ?array
    {
        return $this->request(sprintf("/events/%d/decks/%s/validity", $eventId, $identifier), 'GET');
    }
}