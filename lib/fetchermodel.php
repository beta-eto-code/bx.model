<?php


namespace Bx\Model;


use Bx\Model\Interfaces\ModelServiceInterface;

class FetcherModel
{
    /**
     * @var ModelServiceInterface
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
     * @var array
     */
    private $addFilter;
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
     * FetcherModel constructor.
     * @param ModelServiceInterface $linkedService
     * @param string $keySave
     * @param string $foreignKey
     * @param string $destKey
     * @param bool $isMultipleValue
     * @param array $addFilter
     */
    public function __construct(
        ModelServiceInterface $linkedService,
        string $keySave,
        string $foreignKey,
        string $destKey,
        bool $isMultipleValue = false,
        array $addFilter = []
    )
    {
        $this->service = $linkedService;
        $this->keySave = $keySave;
        $this->foreignKey = $foreignKey;
        $this->linkedModelKey = $this->destKey = $destKey;
        $this->isMultipleValue = $isMultipleValue;
        $this->addFilter = $addFilter;
    }

    /**
     * @param ModelServiceInterface $linkedService
     * @param string $keySave
     * @param string $foreignKey
     * @param string $destKey
     * @param array $addFilter
     * @return static
     */
    public static function initAsSingleValue(
        ModelServiceInterface $linkedService,
        string $keySave,
        string $foreignKey,
        string $destKey,
        array $addFilter = []
    ): self
    {
        return new static($linkedService, $keySave, $foreignKey, $destKey, false, $addFilter);
    }

    /**
     * @param ModelServiceInterface $linkedService
     * @param string $keySave
     * @param string $foreignKey
     * @param string $destKey
     * @param array $addFilter
     * @return static
     */
    public static function initAsMultipleValue(
        ModelServiceInterface $linkedService,
        string $keySave,
        string $foreignKey,
        string $destKey,
        array $addFilter = []
    ): self
    {
        return new static($linkedService, $keySave, $foreignKey, $destKey, true, $addFilter);
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
     * @param array $listKeyValues
     * @return ModelCollection
     */
    private function getLinkedCollection(array $listKeyValues): ModelCollection
    {
        $filter = array_merge($this->addFilter, ["={$this->destKey}" => $listKeyValues]);
        return $this->service->getList(['filter' => $filter]);
    }

    /**
     * @param ModelCollection $collection
     */
    private function fillAsMultipleValue(ModelCollection $collection)
    {
        $listKeyValues = [];
        foreach($collection->column($this->foreignKey) as $value) {
            $listKeyValues = array_merge($listKeyValues, $value);
        }

        if (empty($listKeyValues)) {
            return;
        }

        $linkedCollection = $this->getLinkedCollection($listKeyValues);
        foreach ($collection as $model) {
            $originalValue = $model[$this->foreignKey] ?? null;
            if (empty($originalValue)) {
                continue;
            }

            $resultList = [];
            foreach ($linkedCollection as $linkedModel) {
                $likedValue = $linkedModel[$this->linkedModelKey] ?? null;
                if (!empty($likedValue) && is_array($originalValue) && in_array($likedValue, $originalValue)) {
                    $resultList[] = $linkedModel;
                    if (empty($class)) {
                        $class = get_class($linkedModel);
                    }
                }
            }

            if (!empty($class)) {
                $model[$this->keySave] = new ModelCollection($resultList, $class);
            }
        }
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

        $linkedCollection = $this->getLinkedCollection($listKeyValues);
        foreach ($collection as $model) {
            $originalValue = $model[$this->foreignKey] ?? null;
            if (empty($originalValue)) {
                continue;
            }

            foreach ($linkedCollection as $linkedModel) {
                $likedValue = $linkedModel[$this->linkedModelKey] ?? null;
                if (!empty($likedValue) && $originalValue == $likedValue) {
                    $model[$this->keySave] = $linkedModel;
                }
            }
        }
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
}