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
}