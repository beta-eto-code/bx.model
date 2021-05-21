<?php


namespace Bx\Model;


use Bx\Model\ModelCollection;
use Bx\Model\Interfaces\ModelQueryInterface;
use Bx\Model\Interfaces\Models\PaginationInterface;
use Bx\Model\Interfaces\Models\QueryableModelServiceInterface;
use Bx\Model\Interfaces\QueryInterface;
use Bx\Model\Interfaces\UserContextInterface;

class QueryModel implements ModelQueryInterface
{
    /**
     * @var QueryableModelServiceInterface
     */
    private $modelService;
    /**
     * @var array
     */
    private $filter;
    /**
     * @var array
     */
    private $sort;
    /**
     * @var UserContextInterface|null
     */
    private $userContext;
    /**
     * @var int
     */
    private $limit = 0;
    /**
     * @var int
     */
    private $page = 1;
    /**
     * @var array
     */
    private $select;
    /**
     * @var int
     */
    private $totalCount;

    public function __construct(QueryableModelServiceInterface $modelService, UserContextInterface $userContext = null)
    {
        $this->modelService = $modelService;
        $this->userContext = $userContext;
    }

    /**
     * @param array $params
     * @return ModelQueryInterface
     */
    public function loadFiler(array $params): ModelQueryInterface
    {
        $this->filter = $this->modelService->getFilter($params);
        return $this;
    }

    /**
     * @param array $params
     * @return ModelQueryInterface
     */
    public function loadSort(array $params): ModelQueryInterface
    {
        $this->sort = $this->modelService->getSort($params);
        return $this;
    }

    /**
     * @return ModelCollection
     */
    public function getList(): ModelCollection
    {
        $params = [
            'filter' => $this->filter ?? [],
            'order' => $this->sort ?? [],
        ];

        if ($this->limit > 0) {
            $params['limit'] = $this->limit;
            $params['offset'] = $this->getOffset();
        }

        if (!empty($this->select)) {
            $params['select'] = $this->select;
        }

        return $this->modelService->getList($params, $this->userContext);
    }

    /**
     * @inheritDoc
     */
    public function getListPaginationData(): array
    {
        return [
            'items' => $this->getList()->getApiModel(),
            'pagination' => $this->getPagination()->toArray(),
        ];
    }

    /**
     * @param array $filter
     * @return ModelQueryInterface
     */
    public function setFilter(array $filter): QueryInterface
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * @param integer $limit
     * @return ModelQueryInterface
     */
    public function setLimit(int $limit): QueryInterface
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param integer $limit
     * @return ModelQueryInterface
     */
    public function setSort(array $sort): QueryInterface
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @param integer $limit
     * @return ModelQueryInterface
     */
    public function setPage(int $page): QueryInterface
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @param integer $count
     * @return ModelQueryInterface
     */
    public function setTotalCount(int $count): QueryInterface
    {
        $this->totalCount = $count;
        return $this;
    }

    /**
     * @return PaginationInterface
     */
    public function getPagination(): PaginationInterface
    {
        return new Pagination($this);
    }
    
    /**
     * @return array
     */
    public function getSelect(): array
    {
        return $this->select;
    }

    /**
     * @return integer
     */
    public function getLimit(): int
    {
        return (int)$this->limit;
    }

    /**
     * @inheritDoc
     */
    public function getPage(): int
    {
        return (int)$this->page;
    }

    /**
     * @inheritDoc
     */
    public function getTotalCount(): int
    {
        if ((int)$this->totalCount > 0) {
            return $this->totalCount;
        }

        $params = [
            'filter' => $this->filter
        ];

        return $this->modelService->getCount($params, $this->userContext);
    }

    /**
     * @param array $filter
     * @param string|null $prefix
     * @return ModelQueryInterface
     */
    public function addFilter(array $filter, string $prefix = null): ModelQueryInterface
    {
        if (is_null($prefix)) {
            $this->filter = array_merge($this->filter ?? [], $filter);
            return $this;
        }

        $newFilter = [];
        foreach ($filter as $key => $value) {
            $newKey = $key;
            if (strpos($key, '=') === 0) {
                $newKey = "={$prefix}.".substr_replace($key, '', 0, 1);
            } elseif (strpos($key, '>=') === 0) {
                $newKey = ">={$prefix}.".substr_replace($key, '', 0, 2);
            } elseif (strpos($key, '<=') === 0) {
                $newKey = "<={$prefix}.".substr_replace($key, '', 0, 2);
            } elseif (strpos($key, '<>') === 0) {
                $newKey = "<>{$prefix}.".substr_replace($key, '', 0, 2);
            } elseif (strpos($key, '!') === 0) {
                $newKey = "!{$prefix}.".substr_replace($key, '', 0, 1);
            } elseif (strpos($key, '%') === 0) {
                $newKey = "%{$prefix}.".substr_replace($key, '', 0, 1);
            }

            $newFilter[$newKey] = $value;
        }

        $this->filter = array_merge($this->filter ?? [], $newFilter);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function loadPagination(array $params): ModelQueryInterface
    {
        $this->page = $this->modelService->getPage($params) ?? 1;
        $this->limit = $this->modelService->getLimit($params);

        return $this;
    }

    public function setSelect(array $select): ModelQueryInterface
    {
        $this->select = $select;
        return $this;
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter ?? [];
    }

    /**
     * @return array
     */
    public function getSort(): array
    {
        return $this->sort ?? [];
    }

    /**
     * @return integer
     */
    public function getOffset(): int
    {
        return $this->limit * ($this->page - 1);
    }
}
