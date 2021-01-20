<?php


namespace Bx\Model\Interfaces;


use Bx\Model\AbsOptimizedModel;
use Bx\Model\ModelCollection;
use Bx\Model\Interfaces\Models\FilterableInterface;
use Bx\Model\Interfaces\Models\LimiterInterface;
use Bx\Model\Interfaces\Models\SortableInterface;
use Bitrix\Main\Result;
use Bx\Model\Interfaces\UserContextInterface;

interface ModelServiceInterface extends FilterableInterface, SortableInterface, LimiterInterface
{
    /**
     * Получаем построить запроса
     * @param UserContextInterface|null $userContext
     * @return ModelQueryInterface
     */
    public function query(UserContextInterface $userContext = null): ModelQueryInterface;

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
     * Удаляем элемент по идентификатору
     * @param int $id
     * @param UserContextInterface|null $userContext
     * @return mixed
     */
    public function delete(int $id, UserContextInterface $userContext = null): Result;

    /**
     * Сохраняем модель в базе
     * @param AbsOptimizedModel $model
     * @param UserContextInterface|null $userContext
     * @return mixed
     */
    public function save(AbsOptimizedModel $model, UserContextInterface $userContext = null): Result;
}
