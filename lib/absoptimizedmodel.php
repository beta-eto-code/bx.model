<?php


namespace Bx\Model;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;
use ArrayIterator;
use Bx\Model\Interfaces\ModelInterface;
use Bx\Model\Traits\EntityObjectHelper;
use Exception;
use Iterator;

abstract class AbsOptimizedModel implements ModelInterface
{
    use EntityObjectHelper;

    /**
     * @var array|EntityObject
     */
    protected $data;

    abstract protected function toArray(): array;

    /**
     * @param $data
     * @throws Exception
     */
    public function __construct($data)
    {
        if (!is_array($data) && !($data instanceof EntityObject)) {
            throw new Exception('Invalid data type');
        }

        $this->data = $data;
        if ($data instanceof EntityObject) {
            $this->reflectEntityObject();
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function assertValueByKey(string $key, $value): bool
    {
        return $this->hasValueKey($key) && $this->getValueByKey($key) == $value;
    }

    /**
     * @param string $key
     * @return boolean
     */
    public function hasValueKey(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getValueByKey(string $key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->getApiModel();
    }

    /**
     * @deprecated
     * @param string $key
     * @return boolean
     */
    public function isFill(string $key): bool
    {
       return $this->hasValueKey($key);
    }

    /**
     * @return array
     */
    public function getApiModel(): array
    {
        $result = $this->toArray();
        foreach ($result as &$value) {
            if ($value instanceof DateTime || $value instanceof \DateTime) {
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

    /**
     * @param mixed $offset
     * @return bool
     * @throws ArgumentException
     * @throws SystemException
     */
    public function offsetExists($offset): bool
    {
        if ($this->data instanceof EntityObject) {
            return $this->data->offsetExists($offset);
        }

        return $this->hasValueKey($offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     * @throws ArgumentException
     * @throws SystemException
     */
    public function offsetGet($offset)
    {
        if ($this->data instanceof EntityObject) {
            return $this->data->offsetGet($offset);
        }

        return $this->getValueByKey($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws ArgumentException
     * @throws SystemException
     */
    public function offsetSet($offset, $value)
    {
        if ($this->data instanceof EntityObject) {
            $this->data->offsetSet($offset, $value);
            return;
        }

        $this->data[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if ($this->data instanceof EntityObject) {
            $this->data->offsetUnset($offset);
            return;
        }

        if (isset($this->data[$offset])) {
            unset($this->data[$offset]);
        }
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        if ($this->data instanceof EntityObject) {
            return $this->getEntityObjectData();
        }

        return $this->data;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->getData());
    }

    /**
     * @param callable $fnMap - function($item): array
     * @return mixed
     */
    public function map(callable $fnMap)
    {
        return $fnMap($this);
    }
}
