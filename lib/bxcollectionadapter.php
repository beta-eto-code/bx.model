<?php

namespace Bx\Model;

use Bitrix\Main\ORM\Query\Result;
use Bitrix\Main\SystemException;
use Bitrix\Main\ORM\Objectify\Collection;
use Bx\Model\Interfaces\ModelCollectionInterface;
use Bx\Model\Interfaces\ModelInterface;
use ReflectionException;

class BxCollectionAdapter
{
    /**
     * @var Result
     */
    private $queryResult;
    /**
     * @var Collection|null
     */
    private $bxCollection;
    /**
     * @var ModelInterface|string
     */
    private $modelClass;

    public function __construct(Result $queryResult, string $modelClass)
    {
        $this->queryResult = $queryResult;
        $this->modelClass = $modelClass;
    }

    /**
     * @return Collection|null
     * @throws SystemException
     */
    public function getBxCollection(): ?Collection
    {
        if ($this->bxCollection instanceof Collection) {
            return $this->bxCollection;
        }

        return $this->bxCollection = $this->queryResult->fetchCollection();
    }

    /**
     * @return ModelCollectionInterface
     * @throws SystemException|ReflectionException
     */
    public function getModelCollection(): ModelCollectionInterface
    {
        $bxCollection = $this->getBxCollection();
        if (empty($bxCollection)) {
            return new ModelCollection([], $this->modelClass);
        }

        return static::castToModelCollection($bxCollection, $this->modelClass);
    }

    /**
     * @param Collection $bxCollection
     * @param string $modelClass
     * @return ModelCollectionInterface
     * @throws ReflectionException
     */
    public static function castToModelCollection(Collection $bxCollection, string $modelClass): ModelCollectionInterface
    {
        $collection = new ModelCollection([], $modelClass);
        foreach ($bxCollection as $bxModel) {
            $collection->add(BxModelAdapter::init($bxModel)->getObjectData());
        }

        return $collection;
    }
}