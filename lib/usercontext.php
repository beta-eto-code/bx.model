<?php


namespace Bx\Model;


use Bx\Model\Interfaces\AccessStrategyInterface;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\Models\User;

class UserContext implements UserContextInterface
{
    /**
     * @var User
     */
    private $user;
    /**
     * @var AccessStrategyInterface
     */
    private $accessStrategy;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserId(): int
    {
        return $this->user->getId();
    }

    public function hasAccessOperation(int $operationId): bool
    {
        if ($this->accessStrategy instanceof AccessStrategyInterface) {
            return $this->accessStrategy->checkAccess($this->getUser(), $operationId);
        }

        return true;
    }

    public function setAccessStrategy(AccessStrategyInterface $accessStrategy)
    {
        $this->accessStrategy = $accessStrategy;
    }
}