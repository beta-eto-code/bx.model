<?php


namespace Bx\Model;

use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\ORM\Fields\Field;
use Bx\Model\Interfaces\ModelQueryInterface;
use Bx\Model\Interfaces\Models\PaginationInterface;
use Bx\Model\Interfaces\Models\QueryableModelServiceInterface;
use Bx\Model\Interfaces\UserContextInterface;

class QueryModel extends Query implements ModelQueryInterface
{
    /**
     * @var QueryableModelServiceInterface
     */
    private $modelService;
    /**
     * @var UserContextInterface|null
     */
    private $userContext;
    /**
     * @var ExpressionField[]
     */
    private $runtimeFields;

    public function __construct(QueryableModelServiceInterface $modelService, UserContextInterface $userContext = null)
    {
        $this->modelService = $modelService;
        $this->userContext = $userContext;
    }

    /**
     * @param array $params
     * @return ModelQueryInterface
     */
    public function loadFilter(array $params): ModelQueryInterface
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
        $params = [];

        if ($this->hasFilter()) {
            $params['filter'] = $this->filter;
        }

        if ($this->hasSort()) {
            $params['order'] = $this->sort;
        }

        if ($this->hasLimit()) {
            $params['limit'] = $this->limit;
            $params['offset'] = $this->getOffset();
        }

        if ($this->hasSelect()) {
            $params['select'] = $this->select;
        }

        if ($this->hasRuntimeFields()) {
            $params['runtime'] = $this->runtimeFields;
        }

        if ($this->hasGroup()) {
            $params['group'] = $this->group;
        }

        if ($this->hasFetchList()) {
            $params['fetch'] = $this->fetchList;
        }

        return $this->modelService->getList($params, $this->userContext);
    }

    /**
     * @param string $fieldName
     * @param Field $expression
     * @return self
     */
    public function setRuntimeField(string $fieldName, Field $expression): ModelQueryInterface
    {
        $this->runtimeFields[$fieldName] = $expression;
        return $this;
    }

    /**
     * @return Field[]
     */
    public function getRuntimeFields(): array
    {
        return (array)($this->runtimeFields ?? []);
    }

    public function hasRuntimeFields(): bool
    {
        return !empty($this->runtimeFields);
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
     * @return PaginationInterface
     */
    public function getPagination(): PaginationInterface
    {
        return new Pagination($this);
    }

    /**
     * @inheritDoc
     */
    public function getTotalCount(): int
    {
        if ((int)$this->totalCount > 0) {
            return $this->totalCount;
        }

        $params = [];

        if ($this->hasFilter()) {
            $params['filter'] = $this->filter;
        }

        if ($this->hasRuntimeFields()) {
            $params['runtime'] = $this->runtimeFields;
        }

        if ($this->hasGroup()) {
            $params['group'] = $this->group;
        }

        if ($this->hasSelect()) {
            $params['select'] = $this->select;
        }

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
}
