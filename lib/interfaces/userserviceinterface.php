<?php


namespace Bx\Model\Interfaces;


interface UserServiceInterface extends ModelServiceInterface
{
    /**
     * @param string $login
     * @param string $password
     * @return UserContextInterface|null
     */
    public function login(string $login, string $password): ?UserContextInterface;

    /**
     * @return bool
     */
    public function isAuthorized(): bool;

    /**
     * @return UserContextInterface|null
     */
    public function getCurrentUser(): ?UserContextInterface;
}