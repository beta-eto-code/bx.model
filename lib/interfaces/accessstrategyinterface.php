<?php


namespace Bx\Model\Interfaces;


use Bx\Model\Models\User;

interface AccessStrategyInterface
{
    public function checkAccess(User $user, int $operationId): bool;
}