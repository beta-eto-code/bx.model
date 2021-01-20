<?php


namespace Bx\Model;


use Bx\Model\ModelCollection;
use Bx\Model\Interfaces\ModelQueryInterface;
use Bx\Model\Interfaces\Models\PaginationInterface;
use Bx\Model\Interfaces\ModelServiceInterface;
use Bx\Model\Interfaces\UserContextInterface;

class QueryModel implements ModelQueryInterface
{
    /**
     * @var ModelServiceInterface
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

    public function __construct(ModelServiceInterface $modelService, UserContextInterface $userContext = null)
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
            'filter' => $this->filter,
            'order' => $this->sort
        ];

        if ($this->limit > 0) {
            $params['limit'] = $this->limit;
            $params['offset'] = $this->limit * ($this->page - 1);
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

    public function setLimit(int $limit): ModelQueryInterface
    {
        $this->limit = $limit;
        return $this;
    }

    public function setPage(int $page): ModelQueryInterface
    {
        $this->page = $page;
        return $this;
    }

    public function getPagination(): PaginationInterface
    {
        return new Pagination($this);
    }

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
        $params = [
            'filter' => $this->filter
        ];

        return $this->modelService->getCount($params, $this->userContext);
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
}
