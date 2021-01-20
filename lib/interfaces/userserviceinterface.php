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
}