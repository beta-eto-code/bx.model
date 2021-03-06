<?php


namespace Bx\Model\Interfaces;

use Bitrix\Main\Result;
use Bx\Model\Models\User;

interface UserServiceInterface extends ModelServiceInterface
{
    /**
     * @param string $login
     * @param string $password
     * @return UserContextInterface|null
     */
    public function login(string $login, string $password): ?UserContextInterface;

    /**
     * @param string $login
     * @param string $password
     * @return int
     */
    public function findUserIdByLoginPassword(string $login, string $password): int;

    /**
     * @return bool
     */
    public function isAuthorized(): bool;

    /**
     * @param User $user
     * @param string ...$keyListForSave
     * @return Result
     */
    public function saveExtendedData(User $user, string ...$keyListForSave): Result;

    /**
     * @param User $user
     * @param string $password
     * @return Result
     */
    public function updatePassword(User $user, string $password): Result;

    /**
     * @return UserContextInterface|null
     */
    public function getCurrentUser(): ?UserContextInterface;
}
