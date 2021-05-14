<?php

namespace Bx\Model;

use Bx\Model\Interfaces\QueryInterface;

class Query implements QueryInterface
{
    /**
     * @var array
     */
    private $select;
    /**
     * @var array
     */
    private $filter;
    /**
     * @var array
     */
    private $sort;
    /**
     * @var int
     */
    private $limit;
    /**
     * @var int
     */
    private $page;
    /**
     * @var int
     */
    private $totalCount;

    /**
     * @param array $select
     * @return $this
     */
    public function setSelect(array $select): QueryInterface
    {
        $this->select = $select;
        return $this;
    }

    /**
     * @param array $filter
     * @return self
     */
    public function setFilter(array $filter): QueryInterface
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * Указываем максимальное количество элементов
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit): QueryInterface
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param integer $limit
     * @return $this
     */
    public function setSort(array $sort): QueryInterface
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * Указываем текущу страницу (работает совместно с максимальным количеством элементов)
     * @param int $page
     * @return $this
     */
    public function setPage(int $page): QueryInterface
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @param integer $count
     * @return self
     */
    public function setTotalCount(int $count): QueryInterface
    {
        $this->totalCount = $count;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getSelect(): array
    {
        return (array)($this->select ?? []);
    }

    /**
     * Текущая сортировка
     * @return array
     */
    public function getSort(): array
    {
        return (array)($this->sort ?? []);
    }
    
    /**
     * @return int
     */
    public function getLimit(): int
    {
        return (int)($this->limit ?? 0);
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return (int)($this->page ?? 1);
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        return (array)($this->filter ?? []);
    }

    /**
     * Общее количество элементов
     * @return int
     */
    public function getTotalCount(): int
    {
        return (int)($this->totalCount ?? 0);
    }

    /**
     * @return integer
     */
    public function getOffset(): int
    {
        return $this->limit * ($this->page - 1);
    }
}
