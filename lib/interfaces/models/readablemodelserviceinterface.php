<?php

namespace Bx\Model\Interfaces\Models;

use Bx\Model\AbsOptimizedModel;
use Bx\Model\Interfaces\ModelCollectionInterface;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\ModelCollection;
use Bx\Model\Interfaces\DerivativeModelInterface;

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
    /**
     * Получаем коллекцию производных моделей
     * @param DerivativeModelInterface $class
     * @param array $filter
     * @param array $sort
     * @param int $limit
     * @return DerivativeModelInterface[]|ModelCollectionInterface
     */
    public function getModelCollection(string $class, array $filter = null, array $sort = null, int $limit = null): ModelCollectionInterface;
}
