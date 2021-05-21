<?php

namespace Bx\Model\Interfaces\Models;

use Bx\Model\AbsOptimizedModel;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\ModelCollection;

interface ReadableModelServiceInterface
{
    /**
     * Список элементов
     * @param array $params
     * @param UserContextInterface|null $userContext
     * @return ModelCollection
     */
    public function getList(array $params, UserContextInterface $userContext = null): ModelCollection;

    /**
     * Количество элементов
     * @param array $params
     * @param UserContextInterface|null $userContext
     * @return int
     */
    public function getCount(array $params, UserContextInterface $userContext = null): int;

    /**
     * Получаем элемент по идентификтору
     * @param int $id
     * @param UserContextInterface|null $userContext
     * @return AbsOptimizedModel|null
     */
    public function getById(int $id, UserContextInterface $userContext = null): ?AbsOptimizedModel;
}
