<?php


namespace Bx\Model\Services;


use Bitrix\Main\ObjectException;
use Bitrix\Main\Result;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\Interfaces\ModelCollectionInterface;
use Bx\Model\Interfaces\ModelQueryInterface;
use Bx\Model\Interfaces\ModelServiceInterface;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\ModelCollection;
use Bx\Model\QueryModel;
use Bx\Model\Traits\LimiterHelper;

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
        $keys = array_map(function ($value) {
            return (string)$value;
        }, array_keys($filterFields));

        return in_array($fieldName, $filterFields) || in_array($fieldName, $keys);
    }

    /**
     * @param array $params
     * @return array
     * @throws ObjectException
     */
    public function getFilter(array $params): array
    {
        $result = [];
        $filterFields = $this->filterFields ?? [];
        foreach ($params as $key => $value) {
            $postValue= '';
            $prefix = '=';
            if (strpos($key, 'from_') === 0) {
                $prefix = '>=';
                $key = str_replace('from_', '', $key);
            } elseif (strpos($key, 'to_') === 0) {
                $prefix = '<=';
                $key = str_replace('to_', '', $key);
            } elseif (strpos($key, 'like_') === 0) {
                $prefix = '%';
                $key = str_replace('like_', '', $key);
            } elseif (strpos($key, 'flike_') === 0) {
                $postValue = '%';
                $prefix = '';
                $key = str_replace('flike_', '', $key);
            }

            if (strpos($key, 'date_') === 0) {
                $value = new Date($value, 'Y-m-d');
                $key = str_replace('date_', '', $key);
            } elseif (strpos($key, 'datetime_') === 0) {
                $value = new DateTime($value, 'Y-m-d\TH:i:s\Z');
                $key = str_replace('datetime_', '', $key);
            }

            $isStrict = false;
            if (strpos($key, 'strict_') === 0) {
                $key = str_replace('strict_', '', $key);
                $isStrict = true;
            }

            if ($this->allowForFilter($key)) {

                if (is_string($key) && isset($filterFields[$key])) {
                    $key = $filterFields[$key];
                }

                if (is_string($value) && !$isStrict) {
                    $valueList = explode(',', $value);
                    $value = count($valueList) > 1 ? $valueList : $value;
                }

                $result[$prefix.$key] = $value.$postValue;
            }
        }

        return $result;
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function allowForSort(string $fieldName): bool
    {
        $sortFields = $this->sortFields ?? [];
        $keys = array_map(function ($value) {
            return (string)$value;
        }, array_keys($sortFields));

        return in_array($fieldName, $sortFields) || in_array($fieldName, $keys);
    }

    /**
     * @param array $params
     * @return array|string[]
     */
    public function getSort(array $params): array
    {
        $sortFields = $this->sortFields ?? [];
        $fieldSort = $params['field_sort'] ?? null;
        $orderSort = strtolower($params['order_sort']) === 'desc' ? 'desc' : 'asc';

        if (!empty($fieldSort) && static::allowForSort($fieldSort)) {
            if (isset($sortFields[$fieldSort])) {
                $fieldSort = $sortFields[$fieldSort];
            }

            return [$fieldSort => $orderSort];
        }

        return [];
    }

    public function getModelCollection(string $class, array $filter = null, array $sort = null, int $limit = null): ModelCollectionInterface
    {
        return $this->originalService->getModelCollection($class, $filter, $sort, $limit);
    }
}