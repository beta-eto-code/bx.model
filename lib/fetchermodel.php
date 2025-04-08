<?php

namespace Bx\Model;

use Bx\Model\Interfaces\FetcherModelInterface;
use Bx\Model\Interfaces\ModelCollectionInterface;
use Bx\Model\Interfaces\Models\ReadableModelServiceInterface;
use Bx\Model\Interfaces\AggregateModelInterface;
use Bx\Model\Interfaces\DerivativeModelInterface;
use Bx\Model\Interfaces\QueryInterface;
use Exception;

class FetcherModel implements FetcherModelInterface
{
    /**
     * @var ReadableModelServiceInterface
     */
    private $service;
    /**
     * @var string
     */
    private $foreignKey;
    /**
     * @var string
     */
    private $destKey;
    /**
     * @var string
     */
    private $keySave;
    /**
     * @var string
     */
    private $linkedModelKey;
    /**
     * @var bool
     */
    private $isMultipleValue;
    /**
     * @var AggregateModelInterface|string|null
     */
    private $classCast;
    /**
     * @var DerivativeModelInterface|string|null
     */
    private $loadAsClass;
    /**
     * @var QueryInterface
     */
    private $query;
    /**
     * @var callable
     */
    private $compareCallback;
    /**
     * @var callable
     */
    private $modifyCallback;

    /**
     * @var array
     */
    private $prefixFieldsForReplace;

    /**
     * FetcherModel constructor.
     * @param ReadableModelServiceInterface $linkedService
     * @param string $keySave
     * @param string $foreignKey
     * @param string $destKey
     * @param bool $isMultipleValue
     * @param QueryInterface|null $query
     */
    public function __construct(
        ReadableModelServiceInterface $linkedService,
        string $keySave,
        string $foreignKey,
        string $destKey,
        bool $isMultipleValue = false,
        QueryInterface $query = null
    ) {
        $this->classCast = null;
        $this->service = $linkedService;
        $this->keySave = $keySave;
        $this->foreignKey = $foreignKey;
        $this->linkedModelKey = $this->destKey = $destKey;
        $this->isMultipleValue = $isMultipleValue;
        $this->prefixFieldsForReplace = [];
        if ($query instanceof QueryInterface) {
            $this->query = $query;
        }
    }

    /**
     * @param ReadableModelServiceInterface $linkedService
     * @param string $keySave
     * @param string $foreignKey
     * @param string $destKey
     * @param QueryInterface|null $query
     * @return static
     */
    public static function initAsSingleValue(
        ReadableModelServiceInterface $linkedService,
        string $keySave,
        string $foreignKey,
        string $destKey,
        QueryInterface $query = null
    ): self {
        return new static($linkedService, $keySave, $foreignKey, $destKey, false, $query);
    }

    /**
     * @param ReadableModelServiceInterface $linkedService
     * @param string $keySave
     * @param string $foreignKey
     * @param string $destKey
     * @param QueryInterface|null $query
     * @return static
     */
    public static function initAsMultipleValue(
        ReadableModelServiceInterface $linkedService,
        string $keySave,
        string $foreignKey,
        string $destKey,
        QueryInterface $query = null
    ): self {
        return new static($linkedService, $keySave, $foreignKey, $destKey, true, $query);
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setLinkedModelKey(string $key): self
    {
        $this->linkedModelKey = $key;
        return $this;
    }

    /**
     * @param DerivativeModelInterface|string $derivativeModelClass
     * @return FetcherModelInterface
     * @psalm-suppress MismatchingDocblockParamType
     */
    public function loadAs(string $derivativeModelClass): FetcherModelInterface
    {
        $this->loadAsClass = $derivativeModelClass;
        return $this;
    }

    /**
     * @param array $listKeyValues
     * @return ModelCollection
     * @psalm-suppress InvalidReturnType
     */
    private function getLinkedCollection(array $listKeyValues): ModelCollection
    {
        $addFilter = $this->query instanceof QueryInterface ? $this->query->getFilter() : [];
        $filter = array_merge($addFilter, ["={$this->destKey}" => $listKeyValues]);
        $params = ['filter' => $filter];

        if ($this->query instanceof QueryInterface) {
            if ($this->query->hasSelect()) {
                $params['select'] = $this->query->getSelect();
            }

            if ($this->query->hasLimit()) {
                $params['limit'] = $this->query->getLimit();
            }

            if ($this->query->hasSort()) {
                $params['order'] = $this->query->getSort();
            }

            if ($this->query->hasFetchList()) {
                $params['fetch'] = $this->query->getFetchList();
            }
        }

        if (!empty($this->loadAsClass)) {
            /**
             * @psalm-suppress InvalidReturnStatement,PossiblyInvalidArgument
             */
            $collection = $this->service->getModelCollection(
                $this->loadAsClass,
                $params['filter'] ?? null,
                $params['order'] ?? null,
                $params['limit'] ?? null
            );

            /**
             * @psalm-suppress InvalidArgument
             */
            return $this->replacePrefixAndGetCollection($collection);
        }

        $collection = $this->service->getList($params);
        return $this->replacePrefixAndGetCollection($collection);
    }

    public function addPrefixFieldsForRemove(string $prefix): void
    {
        $this->prefixFieldsForReplace[$prefix] = '';
    }

    /**
     * @param ModelCollection $collection
     * @return ModelCollection
     * @psalm-suppress InvalidReturnType
     */
    private function replacePrefixAndGetCollection(ModelCollectionInterface $collection): ModelCollection
    {
        if (empty($this->prefixFieldsForReplace)) {
            return $collection;
        }

        $newDataList = [];
        foreach ($collection as $key => $item) {
            $itemData = $this->replacePrefixFromCollectionItemData(iterator_to_array($item));
            $newDataList[$key] = $itemData;
        }

        return $collection->newCollection($newDataList);
    }

    private function replacePrefixFromCollectionItemData(array $itemData): array
    {
        $result = [];
        $keyListForReplace = array_keys($this->prefixFieldsForReplace);
        $newKeyList = array_values($this->prefixFieldsForReplace);
        foreach ($itemData as $itemKey => $itemValue) {
            $itemKey = str_replace($keyListForReplace, $newKeyList, $itemKey);
            $result[$itemKey] = $itemValue;
        }

        return $result;
    }

    /**
     * @param ModelCollection $collection
     */
    private function fillAsMultipleValue(ModelCollection $collection)
    {
        $listKeyValues = [];
        foreach ($collection->column($this->foreignKey) as $value) {
            $listKeyValues = array_merge($listKeyValues, (array)$value);
        }

        if (empty($listKeyValues)) {
            return;
        }

        $listKeyValues = array_unique($listKeyValues);
        $isCallableCallback = $this->compareCallback !== null;
        $hasModifyCallback = $this->modifyCallback !== null;
        $hasClassCast = !empty($this->classCast);

        $linkedCollection = $this->getLinkedCollection($listKeyValues);
        if (!$isCallableCallback) {
            $linkedModelListsByKeys = $this->getLinkedCollectionIndexedByValue($linkedCollection, true);
        }

        foreach ($collection as $model) {
            $originalValue = (array)($model[$this->foreignKey] ?? []);
            if (empty($originalValue)) {
                continue;
            }

            $resultList = [];
            if ($isCallableCallback) {
                foreach ($linkedCollection as $linkedModel) {
                    if (($this->compareCallback)($model, $linkedModel)) {
                        $resultList[] = $linkedModel;
                    }
                }
            } else {
                foreach ($originalValue as $foreignKeyValue) {
                    $foreignKeyValue = $this->getValueAsArrayKey($foreignKeyValue);
                    if ($foreignKeyValue !== null && isset($linkedModelListsByKeys[$foreignKeyValue])) {
                        $resultList = array_merge($resultList, $linkedModelListsByKeys[$foreignKeyValue]);
                    }
                }
            }

            if (empty($class)) {
                $linkedModel = current($resultList);
                if ($linkedModel !== false) {
                    $class = get_class($linkedModel);
                } else {
                    $class = AbsOptimizedModel::class;
                }
            }

            $resultCollection = new ModelCollection($resultList, $class);
            if ($hasModifyCallback) {
                $resultCollection = ($this->modifyCallback)($resultCollection);
            }

            $model[$this->keySave] = $hasClassCast ?
                $this->classCast::init($resultCollection) :
                $resultCollection;

            unset($class);
        }
    }

    /**
     * @param AggregateModelInterface|string $aggregateModelClass
     * @return FetcherModelInterface
     * @throws Exception
     * @psalm-suppress MismatchingDocblockParamType
     */
    public function castTo(string $aggregateModelClass): FetcherModelInterface
    {
        /**
         * @psalm-suppress PossiblyInvalidArgument
         */
        if (!class_exists($aggregateModelClass)) {
            /**
             * @psalm-suppress PossiblyInvalidCast
             */
            throw new Exception("{$aggregateModelClass} is not found!");
        }

        $this->classCast = $aggregateModelClass;
        return $this;
    }

    public function setModifyCallback(callable $fn): FetcherModelInterface
    {
        $this->modifyCallback = $fn;
        return $this;
    }


    /**
     * @param callable $fn
     * @return FetcherModelInterface
     */
    public function setCompareCallback(callable $fn): FetcherModelInterface
    {
        $this->compareCallback = $fn;
        return $this;
    }

    /**
     * @param ModelCollection $collection
     */
    private function fillAsSingleValue(ModelCollection $collection)
    {
        $listKeyValues = $collection->unique($this->foreignKey);
        if (empty($listKeyValues)) {
            return;
        }

        $isCallableCallback = $this->compareCallback !== null;
        $hasModifyCallback = $this->modifyCallback !== null;

        $linkedCollection = $this->getLinkedCollection($listKeyValues);
        if (!$isCallableCallback) {
            $linkedModelsByKeys = $this->getLinkedCollectionIndexedByValue($linkedCollection, false);
        }

        foreach ($collection as $model) {
            $originalValue = $model[$this->foreignKey] ?? null;
            if ($originalValue === null) {
                continue;
            }

            if ($isCallableCallback) {
                foreach ($linkedCollection as $linkedModel) {
                    if (($this->compareCallback)($model, $linkedModel)) {
                        $model[$this->keySave] = $hasModifyCallback ?
                            ($this->modifyCallback)($linkedModel) :
                            $linkedModel;
                    }
                }
            } else {
                $originalValue = $this->getValueAsArrayKey($originalValue);
                if ($originalValue !== null && isset($linkedModelsByKeys[$originalValue])) {
                    $linkedModel = $linkedModelsByKeys[$originalValue];
                    $model[$this->keySave] = $hasModifyCallback ? ($this->modifyCallback)($linkedModel) : $linkedModel;
                }
            }
        }
    }

    private function getLinkedCollectionIndexedByValue(ModelCollection $linkedCollection, bool $isMultipleMode): array
    {
        $indexedCollection = [];
        foreach ($linkedCollection as $linkedModel) {
            $linkedValue = isset($linkedModel[$this->linkedModelKey]) ?
                $this->getValueAsArrayKey($linkedModel[$this->linkedModelKey]) :
                null;
            if ($linkedValue !== null) {
                if ($isMultipleMode) {
                    $indexedCollection[$linkedValue][] = $linkedModel;
                } else {
                    $indexedCollection[$linkedValue] = $linkedModel;
                }
            }
        }

        return $indexedCollection;
    }

    /**
     * @param mixed $value
     * @return int|string|null
     */
    private function getValueAsArrayKey($value)
    {
        if (is_string($value)) {
            if (is_numeric($value)) {
                $value = +$value;
            } else {
                return $value;
            }
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            $intValue = (int)$value;
            return $value == $intValue ? $intValue : null;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return $value->__toString();
        }

        return null;
    }

    /**
     * @param ModelCollection $collection
     */
    public function fill(ModelCollection $collection)
    {
        if ($this->isMultipleValue) {
            $this->fillAsMultipleValue($collection);
        } else {
            $this->fillAsSingleValue($collection);
        }
    }

    /**
     * @return QueryInterface
     */
    public function getQuery(): QueryInterface
    {
        return $this->query;
    }

    /**
     * @param QueryInterface $query
     */
    public function setQuery(QueryInterface $query): void
    {
        $this->query = $query;
    }
}
