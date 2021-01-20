<?php


namespace Bx\Model\Interfaces;


use Bx\Model\ModelCollection;
use Bx\Model\Interfaces\Models\PaginationInterface;

interface ModelQueryInterface
{
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
     * Указываем текущу страницу (работает совместно с максимальным количеством элементов)
     * @param int $page
     * @return $this
     */
    public function setPage(int $page): self;

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
