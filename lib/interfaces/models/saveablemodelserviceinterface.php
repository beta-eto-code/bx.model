<?php

namespace Bx\Model\Interfaces\Models;

use Bitrix\Main\Result;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\Interfaces\UserContextInterface;

interface SaveableModelServiceInterface
{
    /**
     * Сохраняем модель в базе
     * @param AbsOptimizedModel $model
     * @param UserContextInterface|null $userContext
     * @return mixed
     */
    public function save(AbsOptimizedModel $model, UserContextInterface $userContext = null): Result;
}
