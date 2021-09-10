<?php

namespace Bx\Model;

use Bx\Model\Interfaces\CollectionItemInterface;
use Bx\Model\Interfaces\GroupCollectionInterface;
use Bx\Model\Interfaces\ReadableCollectionInterface;

class GroupCollection extends Collection implements GroupCollectionInterface
{
    private $key;
    private $value;

    public function __construct($key, $value, CollectionItemInterface ...$itemList)
    {
        $this->key = $key;
        $this->value = $value;
        parent::__construct(...$itemList);
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function assertValueByKey(string $key, $value): bool
    {
        return $this->getValueByKey($key) == $value;
    }

    /**
     * @param string $key
     * @return boolean
     */
    public function hasValueKey(string $key): bool
    {
        return in_array($key, ['key', 'value']);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getValueByKey(string $key)
    {
        if ($key === 'key') {
            return $this->key;
        }

        if ($key === 'value') {
            return $this->value;
        }

        return null;
    }

    /**
     * @param string $key
     * @param mixed ...$value
     * @return CollectionItemInterface[]|ReadableCollectionInterface
     */
    public function filterByKey(string $key, ...$value): ReadableCollectionInterface
    {
        $newCollection = new static($this->key, $this->value);
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
        $newCollection = new static($this->key, $this->value);
        foreach($this as $item) {
            if ($fn($item) === true) {
                $newCollection->append($item);
            }
        }

        return $newCollection;
    }

    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
            'list' => parent::jsonSerialize(),
        ];
    }
}
