<?php


namespace Bx\Model\Interfaces;


use Bx\Model\ModelCollection;
use Bx\Model\Interfaces\Models\PaginationInterface;

interface ModelQueryInterface
{
    /**
     * @param array $select
     * @return $this
     */
    public function setSelect(array $select): self;

    /**
     * Форимируем условия фильтра из переданных данных
     * @param array $params
     * @return $this
     */
    public function loadFiler(array $params): self;

    /**
     * Формируем условие сортировки из переданных данных
     * @param array $params
     * @return $this
     */
    public function loadSort(array $params): self;

    /**
     * Формируем параметры для пагинации
     * @param array $params
     * @return $this
     */
    public function loadPagination(array $params): self;

    /**
     * Возвращает объект пагинации
     * @return PaginationInterface
     */
    public function getPagination(): PaginationInterface;

    /**
     * Возвращает массив с элементами и данными о пагинации
     * @return array
     */
    public function getListPaginationData(): array;

    /**
     * Указываем максимальное количество элементов
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit): self;

    /**
     * @param array $filter
     * @param string|null $prefix
     * @return $this
     */
    public function addFilter(array $filter, string $prefix = null): self;

    /**
     * Указываем текущу страницу (работает совместно с максимальным количеством элементов)
     * @param int $page
     * @return $this
     */
    public function setPage(int $page): self;

    /**
     * Текущий фильтр
     * @return array
     */
    public function getFilter(): array;

    /**
     * Текущая сортировка
     * @return array
     */
    public function getSort(): array;

    /**
     * @return int
     */
    public function getLimit(): int;

    /**
     * @return int
     */
    public function getPage(): int;

    /**
     * Список элементов
     * @return ModelCollection
     */
    public function getList(): ModelCollection;

    /**
     * Общее количество элементов
     * @return int
     */
    public function getTotalCount(): int;
}
