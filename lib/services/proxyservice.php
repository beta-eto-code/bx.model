<?php


namespace Bx\Model\Services;


use Bitrix\Main\Result;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\Helper\FilterParser;
use Bx\Model\Helper\SortRuleParser;
use Bx\Model\Interfaces\ModelCollectionInterface;
use Bx\Model\Interfaces\ModelQueryInterface;
use Bx\Model\Interfaces\ModelServiceInterface;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\ModelCollection;
use Bx\Model\QueryModel;
use Bx\Model\Traits\LimiterHelper;
use Exception;

class ProxyService implements ModelServiceInterface
{
    use LimiterHelper;

    /**
     * @var ModelServiceInterface
     */
    private $originalService;
    /**
     * @var array
     */
    private $sortFields;
    /**
     * @var array
     */
    private $filterFields;

    public function __construct(ModelServiceInterface $originalService)
    {
        $this->originalService = $originalService;
    }

    public function query(UserContextInterface $userContext = null): ModelQueryInterface
    {
        return new QueryModel($this, $userContext);
    }

    public function getList(array $params, UserContextInterface $userContext = null): ModelCollection
    {
        return $this->originalService->getList($params, $userContext);
    }

    public function getCount(array $params, UserContextInterface $userContext = null): int
    {
        return $this->originalService->getCount($params, $userContext);
    }

    public function getById(int $id, UserContextInterface $userContext = null): ?AbsOptimizedModel
    {
        return $this->originalService->getById($id, $userContext);
    }

    public function delete(int $id, UserContextInterface $userContext = null): Result
    {
        return $this->originalService->delete($id, $userContext);
    }

    public function save(AbsOptimizedModel $model, UserContextInterface $userContext = null): Result
    {
        return $this->originalService->save($model, $userContext);
    }

    public function setSortFields(array $fields)
    {
        $this->sortFields = $fields;
    }

    public function setFilterFields(array $fields)
    {
        $this->filterFields = $fields;
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function allowForFilter(string $fieldName): bool
    {
        $filterFields = $this->filterFields ?? [];
        return FilterParser::allowForFilter($fieldName, $filterFields);
    }

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function getFilter(array $params): array
    {
        $filterFields = $this->filterFields ?? [];
        return FilterParser::getParsedFilter($params, $filterFields);
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function allowForSort(string $fieldName): bool
    {
        $sortFields = $this->sortFields ?? [];
        return SortRuleParser::allowForSort($fieldName, $sortFields);
    }

    /**
     * @param array $params
     * @return array|string[]
     */
    public function getSort(array $params): array
    {
        $sortFields = $this->sortFields ?? [];
        return SortRuleParser::getParsedSort($params, $sortFields);
    }

    public function getModelCollection(string $class, array $filter = null, array $sort = null, int $limit = null): ModelCollectionInterface
    {
        return $this->originalService->getModelCollection($class, $filter, $sort, $limit);
    }
}
