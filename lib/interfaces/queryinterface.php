<?php

declare(strict_types=1);

namespace Bx\Model\Interfaces;

interface QueryInterface
{    
    /**
     * @param array $select
     * @return $this
     */
    public function setSelect(array $select): QueryInterface;
    /**
     * @param array $filter
     * @return self
     */
    public function setFilter(array $filter): QueryInterface;
    /**
     * Указываем максимальное количество элементов
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit): QueryInterface;
    /**
     * @param integer $limit
     * @return QueryInterface
     */
    public function setSort(array $sort): QueryInterface;
    /**
     * Указываем текущу страницу (работает совместно с максимальным количеством элементов)
     * @param int $page
     * @return $this
     */
    public function setPage(int $page): QueryInterface;
    /**
     * @param integer $count
     * @return self
     */
    public function setTotalCount(int $count): QueryInterface;
    /**
     * @return array
     */
    public function getSelect(): array;
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
     * @return array
     */
    public function getFilter(): array;
    /**
     * Общее количество элементов
     * @return int
     */
    public function getTotalCount(): int;
    /**
     * @return integer
     */
    public function getOffset(): int;
}
