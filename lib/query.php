<?php

namespace Bx\Model;

use Bx\Model\Interfaces\QueryInterface;

class Query implements QueryInterface
{
    /**
     * @var array
     */
    protected $select;
    /**
     * @var array
     */
    protected $filter;
    /**
     * @var array
     */
    protected $sort;
    /**
     * @var int
     */
    protected $limit = 0;
    /**
     * @var int
     */
    protected $page = 1;
    /**
     * @var int
     */
    protected $totalCount;
    /**
     * @var array
     */
    protected $fetchList;
    /**
     * @var array
     */
    protected $group;

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
        if ($limit < 0) {
            $limit = 0;
        }

        $this->limit = $limit;
        return $this;
    }

    /**
     * @param array $sort
     * @return $this
     */
    public function setSort(array $sort): QueryInterface
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * Указываем связанные сущности для выборки
     * @param array|null $list
     * @return QueryInterface
     */
    public function setFetchList(?array $list): QueryInterface
    {
        $this->fetchList = $list;
        return $this;
    }

    /**
     * Указываем текущу страницу (работает совместно с максимальным количеством элементов)
     * @param int $page
     * @return $this
     */
    public function setPage(int $page): QueryInterface
    {
        if ($page <= 0) {
            $page = 1;
        }

        $this->page = $page;
        return $this;
    }

    /**
     * @param array $group
     * @return QueryInterface
     */
    public function setGroup(array $group): QueryInterface
    {
        $this->group = $group;
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
     * Связанные сущности для выборки
     * @return array
     */
    public function getFetchList(): array
    {
        return (array)($this->fetchList ?? []);
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
     * @return array
     */
    public function getGroup(): array
    {
        return (array)($this->group ?? []);
    }

    /**
     * @return integer
     */
    public function getOffset(): int
    {
        if ($this->page <= 0) {
            return 0;
        }

        return $this->limit * ($this->page - 1);
    }

    /**
     * @return boolean
     */
    public function hasFetchList(): bool
    {
        return !is_null($this->fetchList);
    }

    /**
     * @return boolean
     */
    public function hasSelect(): bool
    {
        return !empty($this->select);
    }

    /**
     * @return boolean
     */
    public function hasFilter(): bool
    {
        return !empty($this->filter);
    }

    /**
     * @return boolean
     */
    public function hasLimit(): bool
    {
        return !empty($this->limit);
    }

    /**
     * @return boolean
     */
    public function hasSort(): bool
    {
        return !empty($this->sort);
    }

    /**
     * @return boolean
     */
    public function hasGroup(): bool
    {
        return !empty($this->group);
    }
}
