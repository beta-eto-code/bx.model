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
     * @return array
     */
    public function getApiModel(): array
    {
        $result = $this->toArray();
        foreach ($result as &$value) {
            if ($value instanceof DateTime) {
                $value = $value->format('Y-m-d\TH:i:s\Z');
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

    /**
     * @param string $prefix
     * @param array $list
     * @return string[]
     */
    protected static function addPrefix(string $prefix, array $list): array
    {
        foreach ($list as &$item) {
            $item = $prefix.$item;
        }

        return $list;
    }

    protected static function prepareSelect(string $prefix, array $selectFields, array $replacePrefix): string
    {
        $result = [];
        foreach ($selectFields as $key => $field) {
            $iField = $field;
            if (is_string($key)) {
                $iField = $key;
            }

            $oField = strtolower(str_replace($replacePrefix, '', $iField));
            $result[] = "{$prefix}.{$iField} as {$oField}";
        }

        return implode(', ', $result);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }
}
