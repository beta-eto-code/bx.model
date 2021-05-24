<?php

namespace Bx\Model;

use ArrayAccess;
use ArrayIterator;
use Bx\Model\Interfaces\CollectionItemInterface;
use Bx\Model\Interfaces\ReadableCollectionInterface;
use Exception;
use Iterator;

class MappedCollection implements ReadableCollectionInterface, ArrayAccess
{
    /**
     * @var CollectionItemInterface[]
     */
    protected $list;
    /**
     * @var string
     */
    private $key;

    /**
     * @param CollectionItemInterface[] $collection
     * @param string $key
     */
    public function __construct($collection, string $key)
    {
        $this->list = [];
        $this->key = $key;

        foreach($collection as $item) {
            if ($item instanceof CollectionItemInterface && $item->hasValueKey($key)) {
                $indexKey = $item->getValueByKey($key);
                $this->list[$indexKey] = $item;
            }
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return CollectionItemInterface|null
     */
    public function findByKey(string $key, $value): ?CollectionItemInterface
    {
        foreach($this->list as $item) {
            if ($item->hasValueKey($key) && $item->assertValueByKey($key, $value)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param callable $fn - attribute CollectionItemInterface
     * @return CollectionItemInterface|null
     */
    public function find(callable $fn): ?CollectionItemInterface
    {
        foreach($this->list as $item) {
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
        foreach($this->list as $item) {
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
     * @param callable $fn - attribute is mixed value by the key of the collection item
     * @return array
     */
    public function unique(string $key, callable $fnModifier = null): array
    {
        $result = [];
        $isCallable = $fnModifier !== null;
        foreach($this->list as $item) {
            $value = $item->hasValueKey($key) ? $item->getValueByKey($key) : null;
            $result[$value] = $isCallable ? $fnModifier($value) : $value;
        }

        return array_values($result);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return CollectionItemInterface[]|ReadableCollectionInterface
     */
    public function filterByKey(string $key, $value): ReadableCollectionInterface
    {
        return $this->filter(function(CollectionItemInterface $item) use ($key, $value) {
            return $item->hasValueKey($key) && $item->assertValueByKey($key, $value);
        });
    }

    /**
     * @param callable $fn - attribute CollectionItemInterface
     * @return ReadableCollectionInterface
     */
    public function filter(callable $fn): ReadableCollectionInterface
    {
        return new static((array)array_filter($this->list, $fn), $this->key);
    }

    /**
     * @return CollectionItemInterface|null
     */
    public function first(): ?CollectionItemInterface
    {
        $first = current($this->list);
        return $first instanceof CollectionItemInterface ? $first : null;
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->list);
    }

    /**
     * @return integer
     */
    public function count(): int
    {
        return count($this->list);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $result = [];
        foreach($this->list as $item) {
            $result[] = $item->jsonSerialize();
        }

        return $result;
    }

    public function offsetExists($offset)
    {
        return isset($this->list[$offset]) ? true : false;
    }

    public function offsetGet($offset)
    {
        return  $this->list[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        if (!($value instanceof CollectionItemInterface)) {
            throw new Exception('Invalid data type for collection');
        }

        $this->list[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if (isset($this->list[$offset])) {
            unset($this->list[$offset]);
        }
    }
}
