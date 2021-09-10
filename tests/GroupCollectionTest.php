<?php

namespace Bx\Model\Tests;


use Bx\Model\GroupCollection;
use Bx\Model\Interfaces\CollectionInterface;

class GroupCollectionTest extends CollectionTest
{
    /**
     * @param array $data
     * @return Collection
     */
    protected function initCollection(array $data): CollectionInterface
    {
        $collection = new GroupCollection('test', 'one');
        foreach ($data as $item) {
            $collection->append($this->initModel($item));
        }

        return $collection;
    }

    public function testJsonSerialize()
    {
        $assertValue = [
            'key' => 'test',
            'value' => 'one',
            'list' => $this->originalData,
        ];

        $this->assertEquals($this->collection->jsonSerialize(), $assertValue);
    }
}