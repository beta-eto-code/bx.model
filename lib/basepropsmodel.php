<?php

namespace Bx\Model;

use ArrayIterator;
use Iterator;

abstract class BasePropsModel extends AbsOptimizedModel
{
    public function __construct(array $data = [])
    {
        $this->data = new ArrayIterator($this);
        $propsMap = $this->getPropsMap();
        foreach($this as $key => $value) {
            $dataKey = $propsMap[$key] ?? $key;
            if (isset($data[$dataKey])) {
                $this[$key] = $data[$dataKey];
            }
        }
        unset($value);
    }

    protected function getPropsMap(): array
    {
        return [];
    }

    public function getIterator(): Iterator
    {
        if ($this->data instanceof ArrayIterator) {
            return $this->data;
        }

        return $this->data = new ArrayIterator($this);
    }

    public function hasValueKey(string $key): bool
    {
        $data = iterator_to_array($this->data);
        return isset($data[$key]);
    }
}
