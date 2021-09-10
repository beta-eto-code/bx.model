<?php

namespace Bx\Model\Tests;

use Bx\Model\Interfaces\CollectionInterface;
use Bx\Model\Interfaces\ModelInterface;
use Bx\Model\MappedCollection;

class MappedCollectionTest extends CollectionTest
{
    /**
     * @param array $data
     * @return MappedCollection
     */
    protected function initCollection(array $data): CollectionInterface
    {
        $list = [];
        foreach ($data as $item) {
            $list[] = $this->initModel($item);
        }

        return new MappedCollection($list, 'id');
    }

    public function testMap()
    {
        $assertValue = [
            1 => ['id' => '#1'],
            2 => ['id' => '#2'],
            3 => ['id' => '#3'],
        ];
        $result = $this->collection->map(function (ModelInterface $model) {
            return [
                'id' => "#{$model['id']}"
            ];
        });

        $this->assertEquals($result, $assertValue);
    }

    public function testGetIterator()
    {
        $this->assertTrue(method_exists($this->collection, 'getIterator'));
        $this->assertTrue($this->collection->getIterator() instanceof \Iterator);

        $counter = 0;
        foreach ($this->collection as $model) {
            foreach ($model as $key => $value) {
                $this->assertEquals($value, $this->originalData[$counter][$key]);
            }
            $counter++;
        }

        $this->assertCount($counter, $this->originalData);
    }

    public function testOffsetExists()
    {
        $this->assertTrue(isset($this->collection[1]));
        $this->assertTrue($this->collection->offsetExists(1));

        $this->assertFalse(isset($this->collection[6]));
        $this->assertFalse($this->collection->offsetExists(6));
    }

    public function testOffsetGet()
    {
        $assertValue = null;
        foreach ($this->collection as $model) {
            if ($model['id'] === 1) {
                $assertValue = $model;
                break;
            }
        }

        $this->assertEquals($this->collection[1], $assertValue);
        $this->assertEquals($this->collection->offsetGet(1), $assertValue);
    }

    public function testOffsetSet()
    {
        $newModel = $this->initModel([
           'id' => 5,
           'name' => 'name ',
           'boolean' => false,
        ]);

        $this->assertFalse($this->collection->offsetExists(5));
        $this->collection->append($newModel);
        $this->assertTrue($this->collection->offsetExists(5));
        $this->assertEquals($this->collection[5], $newModel);
    }

    public function testOffsetUnset()
    {
        $this->assertTrue($this->collection->offsetExists(2));
        unset($this->collection[2]);
        $this->assertFalse($this->collection->offsetExists(2));
    }
}