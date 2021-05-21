<?php

namespace Bx\Model\Interfaces\Models;

use Bitrix\Main\Result;
use Bx\Model\Interfaces\UserContextInterface;

interface RemoveableModelServiceInterface
{
    /**
     * Удаляем элемент по идентификатору
     * @param int $id
     * @param UserContextInterface|null $userContext
     * @return mixed
     */
    public function delete(int $id, UserContextInterface $userContext = null): Result;
}
