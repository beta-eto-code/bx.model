<?php

namespace Bx\Model\Tests;

use Bx\Model\BasePropsModel;
use Bx\Model\Interfaces\ModelInterface;

class PropsModelTest extends ModelTest
{
    /**
     * @param array $data
     * @return ModelInterface
     */
    protected function initModel(array $data): ModelInterface
    {
        return new class($data) extends BasePropsModel {
            public $key1;
            public $key2;
            public $key3;
            public $date;
            public $boolean;

            protected function toArray(): array
            {
                return iterator_to_array($this->data);
            }
        };
    }
}