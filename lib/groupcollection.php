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
     * @param $list
     * @return ReadableCollectionInterface
     */
    protected function newCollection($list): ReadableCollectionInterface
    {
        return new static($this->key, $this->value, ...$list);
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

    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
            'list' => parent::jsonSerialize(),
        ];
    }
}
