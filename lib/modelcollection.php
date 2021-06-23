<?php


namespace Bx\Model;

use Bx\Model\Interfaces\CollectionInterface;
use Bx\Model\Interfaces\CollectionItemInterface;
use Bx\Model\Interfaces\ModelCollectionInterface;
use Bx\Model\Interfaces\ReadableCollectionInterface;
use SplObjectStorage;
use Bx\Model\Interfaces\ModelInterface;
use Traversable;

class ModelCollection extends Collection implements ModelCollectionInterface
{
    /**
     * @var ModelInterface[]|CollectionItemInterface[]|SplObjectStorage
     */
    protected $items;
    /**
     * @var string
     */
    protected $className;

    public function __construct($list, string $className)
    {
        $this->items = new SplObjectStorage();

        $this->className = $className;
        foreach ($list as $item) {
            if ($item instanceof $className) {
                $this->items->attach($item);
                continue;
            } elseif(is_array($item) || $item instanceof Traversable) {
                $this->items->attach(new $className($item));
            }
        }
    }

    /**
     * @param CollectionItemInterface $item
     * @return void
     */
    public function append(CollectionItemInterface $item)
    {
        if ($item instanceof $this->className) {
            $this->items->attach($item);
        }
    }

    /**
     * @deprecated
     * @param ModelInterface $model
     * @return void
     */
    public function addModel(ModelInterface $model)
    {
        $this->append($model);
    }

    public function add(array $modelData)
    {
        $this->append(new $this->className($modelData));
    }

    /**
     * @param string $key
     * @param string $className
     * @return ModelCollectionInterface
     */
    public function collection(string $key, string $className): ModelCollectionInterface
    {
        $result = new ModelCollection([], $className);
        $list = $this->column($key);
        foreach($list as $item) {
            $this->addItemInCollection($result, $item);
        }

        return $result;
    }

    /**
     * @param ModelCollectionInterface $collection
     * @param mixed $item
     * @return void
     */
    private function addItemInCollection(ModelCollectionInterface $collection, $item)
    {
        if ($item instanceof CollectionItemInterface) {
            $collection->append($item);
        } elseif (is_array($item)) {
            $collection->add($item);
        } elseif ($item instanceof CollectionInterface) {
            foreach($item as $subItem) {
                $this->addItemInCollection($collection, $subItem);
            }
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return ModelInterface[]|ModelCollection
     */
    public function filterByKey(string $key, $value): ReadableCollectionInterface
    {
        $newCollection = new static([], $this->className);
        foreach($this as $item) {
            if ($item->hasValueKey($key) && $item->assertValueByKey($key, $value)) {
                $newCollection->append($item);
            }
        }

        return $newCollection;
    }

    /**
     * @deprecated
     * @param string $fieldName
     * @param $value
     * @return $this
     */
    public function filerByColumn(string $fieldName, $value): self
    {
        return $this->filterByKey($fieldName, $value);
    }

    /**
     * @param callable $fn
     * @return ReadableCollectionInterface
     */
    public function filter(callable $fn): ReadableCollectionInterface
    {
        return new static(array_filter(iterator_to_array($this->items), $fn), $this->className);
    }

    /**
     * @param string $fieldName
     * @param $value
     * @return ModelInterface|null
     */
    public function findByColumn(string $fieldName, $value): ?CollectionItemInterface
    {
        return $this->find(function ($item) use ($fieldName, $value) {
            return isset($item[$fieldName]) && $item[$fieldName] == $value;
        });
    }

    /**
     * @param int $index
     * @return ModelInterface|null
     */
    public function getByIndex(int $index): ?ModelInterface
    {
        $list = iterator_to_array($this->items) ?? [];
        return $list[$index] ?? null;
    }

    /**
     * @return array
     */
    public function getApiModel(): array
    {
        $result = [];
        foreach($this as $item) {
            $result[] = $item->jsonSerialize();
        }

        return $result;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->getApiModel();
    }
}
