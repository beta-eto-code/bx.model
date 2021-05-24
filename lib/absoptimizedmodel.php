<?php


namespace Bx\Model;

use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;;
use ArrayIterator;
use Bx\Model\Interfaces\ModelInterface;

abstract class AbsOptimizedModel implements ModelInterface
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
     * @param [type] $value
     * @return boolean
     */
    public function assertValueByKey(string $key, $value): bool
    {
        return $this->hasValueKey($key) ? $this->getValueByKey($key) == $value : false;
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
        return $this->hasValueKey($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getValueByKey($offset);
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
