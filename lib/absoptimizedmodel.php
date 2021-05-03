<?php


namespace Bx\Model;

use ArrayAccess;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;
use IteratorAggregate;
use ArrayIterator;

abstract class AbsOptimizedModel implements ArrayAccess, IteratorAggregate
{
    /**
     * @var array
     */
    protected $data;

    abstract protected function toArray(): array;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     * @return boolean
     */
    public function isFill(string $key): bool
    {
       return isset($this->data[$key]); 
    }

    /**
     * @return array
     */
    public function getApiModel(): array
    {
        $result = $this->toArray();
        foreach ($result as &$value) {
            if ($value instanceof DateTime) {
                $value = $value->format('c');
                continue;
            }
            if ($value instanceof Date) {
                $value = $value->format('Y-m-d');
            }
        }
        unset($value);

        return $result;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if (isset($this->data[$offset])) {
            unset($this->data[$offset]);
        }
    }

    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }
}
