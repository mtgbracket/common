<?php


namespace Mtgbracket\Service;


use Mtgbracket\Service\Abstraction\BaseMicroservice;

/**
 * Class AccountsService
 * @package Mtgbracket\Service
 */
class AccountsService extends BaseMicroservice
{
    /**
     * @return string
     */
    protected function getDefaultEndpoint(): string
    {
        return 'http://accounts.mtgbracket.com';
    }

    /**
     * @return string
     */
    protected function getDefaultServiceName(): string
    {
        return 'accounts';
    }

    /**
     * @param int|null $userId
     * @return array|null
     */
    public function getUser(?int $userId): ?array
    {
        return $this->request(sprintf("users/%d/", ($userId !== null) ? $userId : 'me'), 'GET');
    }

    /**
     * @param int $userId
     * @return array|null
     */
    public function getFollowers(int $userId): ?array
    {
        return $this->request(sprintf("users/%d/followers", $userId), 'GET');
    }

    /**
     * @param int $organizationId
     * @return array|null
     */
    public function getOrganization(int $organizationId): ?array
    {
        return $this->request(sprintf("organizations/%d/", $organizationId), 'GET');
    }

    /**
     * @param int $organizationId
     * @param int $userId
     * @return array|null
     */
    public function getAuthorizedUser(int $organizationId, int $userId): ?array
    {
        return $this->request(sprintf("organizations/%d/users/%d", $organizationId, $userId), 'GET');
    }

}