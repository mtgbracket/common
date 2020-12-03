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
}