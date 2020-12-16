<?php


namespace Mtgbracket\Service;


use Mtgbracket\Service\Abstraction\BaseMicroservice;

/**
 * Class EventsService
 * @package Mtgbracket\Service
 */
class EventsService extends BaseMicroservice
{
    /**
     * @return string
     */
    protected function getDefaultEndpoint(): string
    {
        return 'http://events.mtgbracket.com';
    }

    /**
     * @return string
     */
    protected function getDefaultServiceName(): string
    {
        return 'events';
    }

    /**
     * @param int $eventId
     * @return array|null
     */
    public function getEvent(int $eventId): ?array
    {
        return $this->request(sprintf("/events/%d", $eventId), 'GET');
    }
}