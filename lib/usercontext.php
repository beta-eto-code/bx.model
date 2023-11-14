<?php

namespace Bx\Model;

use Bx\Model\Interfaces\AccessStrategyInterface;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\Models\User;

class UserContext implements UserContextInterface
{
    private User $user;
    private ?AccessStrategyInterface $accessStrategy = null;
    private array $params;

    public function __construct(User $user, array $params = [])
    {
        $this->user = $user;
        $this->params = $params;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserId(): int
    {
        return $this->user->getId();
    }

    public function hasAccessOperation(int $operationId, string $scope = ''): bool
    {
        if ($this->accessStrategy instanceof AccessStrategyInterface) {
            return $this->accessStrategy->checkAccess($this->getUser(), $operationId, $scope);
        }

        return true;
    }

    public function setAccessStrategy(AccessStrategyInterface $accessStrategy)
    {
        $this->accessStrategy = $accessStrategy;
    }

    public function getParam(string $key)
    {
        return $this->params[$key] ?? null;
    }

    public function setParam(string $key, $value): void
    {
        $this->params[$key] = $value;
    }
}
