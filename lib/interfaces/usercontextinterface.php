<?php

namespace Bx\Model\Interfaces;

use Bx\Model\Models\User;

interface UserContextInterface
{
    /**
     * @return User
     */
    public function getUser(): User;

    /**
     * @return int
     */
    public function getUserId(): int;

    /**
     * @param int $operationId
     * @param string $scope
     * @return bool
     */
    public function hasAccessOperation(int $operationId, string $scope = ''): bool;

    /**
     * @param AccessStrategyInterface $accessStrategy
     * @return void
     */
    public function setAccessStrategy(AccessStrategyInterface $accessStrategy);
}
