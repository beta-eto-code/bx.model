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
     * @param string $key
     * @return mixed
     */
    public function getParam(string $key);

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setParam(string $key, $value): void;

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
