<?php

namespace Bx\Model;

use ArrayIterator;
use Bx\Model\Interfaces\CollectionInterface;
use Bx\Model\Interfaces\CollectionItemInterface;
use Bx\Model\Interfaces\GroupCollectionInterface;
use Bx\Model\Interfaces\ReadableCollectionInterface;
use SplObjectStorage;

class Collection implements CollectionInterface
{
    /**
     * @var CollectionItemInterface[]|SplObjectStorage
     */
    protected $items;

    public function __construct(CollectionItemInterface ...$itemList)
    {
        $this->items = new SplObjectStorage();
        foreach($itemList as $item) {
            $this->append($item);
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return CollectionItemInterface|null
     */
    public function findByKey(string $key, $value): ?CollectionItemInterface
    {
        foreach($this as $item) {
            if ($item->hasValueKey($key) && $item->assertValueByKey($key, $value)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param callable $fn
     * @return array
     */
    public function map(callable $fn): array
    {
        return array_map($fn, iterator_to_array($this->items));
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator(iterator_to_array($this->items));
//        $this->items->rewind();
//        return $this->items;
    }

    /**
     * @param callable $fn - attribute CollectionItemInterface
     * @return CollectionItemInterface|null
     */
    public function find(callable $fn): ?CollectionItemInterface
    {
        foreach($this as $item) {
            if ($fn($item) === true) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param string $key
     * @param string|null $indexKey
     * @param callable|null $fnModifier - attribute is mixed value by the key of the collection item
     * @return array
     */
    public function column(string $key, string $indexKey = null, callable $fnModifier = null): array
    {
        $result = [];
        $isCallable = $fnModifier !== null;
        foreach($this as $item) {
            $itemKey = null;
            if (!empty($indexKey) && $item->hasValueKey($indexKey)) {
                $itemKey = $item->getValueByKey($indexKey);
            }

            $value = $item->hasValueKey($key) ? $item->getValueByKey($key) : null;
            if (empty($itemKey)) {
                $result[] = $isCallable ? $fnModifier($value) : $value;
            } else {
                $result[$itemKey] = $isCallable ? $fnModifier($value) : $value;
            }
        }

        return $result;
    }

    /**
     * @param string $key
     * @param callable|null $fnModifier - attribute is mixed value by the key of the collection item
     * @return array
     */
    public function unique(string $key, callable $fnModifier = null): array
    {
        $result = [];
        $isCallable = $fnModifier !== null;
        foreach($this as $item) {
            $value = $item->hasValueKey($key) ? $item->getValueByKey($key) : null;
            $result[$value] = $isCallable ? $fnModifier($value) : $value;
        }

        return array_values($result);
    }

    /**
     * @param string $key
     * @param mixed ...$value
     * @return CollectionItemInterface[]|ReadableCollectionInterface
     */
    public function filterByKey(string $key, ...$value): ReadableCollectionInterface
    {
        $newCollection = new static;
        if (empty($value)) {
            return $newCollection;
        }

        foreach($this as $item) {
            if ($item->hasValueKey($key) && in_array($item->getValueByKey($key), (array)$value)) {
                $newCollection->append($item);
            }
        }

        return $newCollection;
    }

    /**
     * @param callable $fn - attribute CollectionItemInterface
     * @return ReadableCollectionInterface
     */
    public function filter(callable $fn): ReadableCollectionInterface
    {
        $newCollection = new static;
        foreach($this as $item) {
            if ($fn($item) === true) {
                $newCollection->append($item);
            }
        }

        return $newCollection;
    }

    /**
     * @param CollectionItemInterface $item
     * @return void
     */
    public function append(CollectionItemInterface $item)
    {
        $this->items->attach($item);
    }

    /**
     * @param CollectionItemInterface $item
     * @return void
     */
    public function remove(CollectionItemInterface $item)
    {
        if ($this->items->contains($item)) {
            $this->items->detach($item);
        }
    }

    /**
     * @param string $key
     * @return GroupCollectionInterface[]|ReadableCollectionInterface
     */
    public function groupByKey(string $key): ReadableCollectionInterface
    {
        $list = [];
        foreach($this->items as $item) {
            $index = $item->getValueByKey($key);
            $list[$index][] = $item;
        }

        $collection = new Collection();
        foreach($list as $index => $itemsList) {
            $group = new GroupCollection($key, $index, ...$itemsList);
            $collection->append($group);
        }

        return $collection;
    }

    /**
     * @param callable $fnCalcKeyValue - возвращает значение для группировки
     * @return GroupCollectionInterface[]|ReadableCollectionInterface
     */
    public function group(string $key, callable $fnCalcKeyValue): ReadableCollectionInterface
    {
        $list = [];
        foreach($this->items as $item) {
            $index = $fnCalcKeyValue($item);
            $list[$index][] = $item;
        }

        $collection = new Collection();
        foreach($list as $index => $itemsList) {
            $group = new GroupCollection($key, $index, ...$itemsList);
            $collection->append($group);
        }

        return $collection;
    }

    /**
     * @return integer
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $result = [];
        foreach($this as $item) {
            $result[] = $item->jsonSerialize();
        }

        return $result;
    }

    /**
     * @return SplObjectStorage
     */
    public function getStorage(): SplObjectStorage
    {
        return $this->items;
    }

    /**
     * @return CollectionItemInterface|null
     */
    public function first(): ?CollectionItemInterface
    {
        $current = $this->getIterator()->current();
        return $current instanceof CollectionItemInterface ? $current : null;
    }
}
