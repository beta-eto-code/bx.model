<?php

namespace Bx\Model;

use ArrayIterator;
use Iterator;

abstract class BasePropsModel extends AbsOptimizedModel
{
    public function __construct(array $data = [])
    {
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
        return $this->data = new ArrayIterator($this);
    }
}
