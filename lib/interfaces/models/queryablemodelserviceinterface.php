<?php

namespace Bx\Model\Interfaces\Models;

use Bx\Model\Interfaces\ModelQueryInterface;
use Bx\Model\Interfaces\UserContextInterface;

interface QueryableModelServiceInterface extends ReadableModelServiceInterface, FilterableInterface, SortableInterface, LimiterInterface
{
    /**
     * Получаем построить запроса
     * @param UserContextInterface|null $userContext
     * @return ModelQueryInterface
     */
    public function query(UserContextInterface $userContext = null): ModelQueryInterface;
}
