<?php

namespace Bx\Model\Tests;

use Bx\Model\AbsOptimizedModel;
use Bx\Model\Collection;
use Bx\Model\Interfaces\CollectionInterface;
use Bx\Model\Interfaces\ModelInterface;
use Bx\Model\Interfaces\ReadableCollectionInterface;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @var CollectionInterface|ModelInterface[]
     */
    protected $collection;
    /**
     * @var array[]
     */
    protected $originalData;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->originalData = [
            [
                'id' => 1,
                'name' => 'name 1',
                'boolean' => true,
            ],
            [
                'id' => 2,
                'name' => 'name 2',
                'boolean' => true,
            ],
            [
                'id' => 3,
                'name' => 'name 3',
                'boolean' => false,
            ],
        ];
        $this->collection = $this->initCollection($this->originalData);
    }

    /**
     * @param array $data
     * @return Collection
     */
    protected function initCollection(array $data): CollectionInterface
    {
        $collection = new Collection();
        foreach ($data as $item) {
            $collection->append($this->initModel($item));
        }

        return $collection;
    }

    /**
     * @param array $data
     * @return ModelInterface
     */
    protected function initModel(array $data): ModelInterface
    {
        return new class($data) extends AbsOptimizedModel {

            protected function toArray(): array
            {
                return $this->data;
            }
        };
    }

    public function testGroupByKey()
    {
        $result = $this->collection->groupByKey('boolean');

        $this->assertEquals($result->count(), 2);

        $trueGroup = $result->findByKey('value', true);
        $this->assertEquals($trueGroup->count(), 2);

        $falseGroup = $result->findByKey('value', false);
        $this->assertEquals($falseGroup->count(), 1);
    }

    public function testColumn()
    {
        $valuesById = [1,2,3];
        $valuesByName = ['name 1', 'name 2', 'name 3'];
        $valuesByBoolean = [true, true, false];
        $this->assertEquals($this->collection->column('id'), $valuesById);
        $this->assertEquals($this->collection->column('name'), $valuesByName);
        $this->assertEquals($this->collection->column('boolean'), $valuesByBoolean);

        $assertValues = ['name 1' => '#1', 'name 2' => '#2', 'name 3' => '#3'];
        $result = $this->collection->column('id', 'name', function (int $id) {
            return "#{$id}";
        });
        $this->assertEquals($result, $assertValues);
    }

    public function testFirst()
    {
        $this->assertEquals(
            $this->collection->first(),
            $this->initModel($this->originalData[0])
        );
    }

    public function testFilter()
    {
        $assertValue = $this->initCollection([]);
        foreach ($this->collection as $model) {
            if ($model['boolean'] === true) {
                $assertValue->append($model);
            }
        }

        $result = $this->collection->filter(function (ModelInterface $model) {
            return $model['boolean'] === true;
        });

        $this->assertEquals($result, $assertValue);
    }

    public function testGetIterator()
    {
        $this->assertTrue(method_exists($this->collection, 'getIterator'));
        $this->assertTrue($this->collection->getIterator() instanceof  \Iterator);

        $counter = 0;
        foreach ($this->collection as $i => $model) {
            $counter++;
            foreach ($model as $key => $value) {
                $this->assertEquals($value, $this->originalData[$i][$key]);
            }
        }

        $this->assertCount($counter, $this->originalData);
    }

    public function testFindByKey()
    {
        $assertValue = null;
        foreach ($this->collection as $model) {
            if ($model['id'] === 1) {
                $assertValue = $model;
                break;
            }
        }

        $this->assertEquals($this->collection->findByKey('id', 1), $assertValue);
    }

    public function testUnique()
    {
        $assertValue = [true, false];
        $result = $this->collection->unique('boolean');
        $this->assertEquals($result, $assertValue);

        $assertValue = ['yes', 'no'];
        $result = $this->collection->unique('boolean', function (bool $boolean) {
           return $boolean ? 'yes' : 'no';
        });
        $this->assertEquals($result, $assertValue);
    }

    public function testJsonSerialize()
    {
        $this->assertEquals($this->collection->jsonSerialize(), $this->originalData);
    }

    public function testFilterByKey()
    {
        $assertValue = $this->initCollection([]);
        foreach ($this->collection as $model) {
            if ($model['boolean'] === true) {
                $assertValue->append($model);
            }
        }

        $result = $this->collection->filterByKey('boolean', true);
        $this->assertEquals($result, $assertValue);

        $assertValue = $this->initCollection([]);
        foreach ($this->collection as $model) {
            if (in_array($model['id'], [1,2])) {
                $assertValue->append($model);
            }
        }

        $result = $this->collection->filterByKey('id', 1, 2);
        $this->assertEquals($result, $assertValue);
    }

    public function testAppend()
    {
        $this->assertEquals($this->collection->count(), 3);
        $newModel = $this->initModel([
            'id' => 4,
            'name' => 'name 4',
            'boolean' => false
        ]);

        $this->collection->append($newModel);
        $lastModel = null;
        foreach ($this->collection as $model) {
            $lastModel = $model;
        }

        $this->assertEquals($this->collection->count(), 4);
        $this->assertEquals($lastModel, $lastModel);
    }

    public function testGroup()
    {
        $result = $this->collection->group('tier', function (ModelInterface $model) {
            return $model['id'] < 3 ? 'low' : 'other';
        });

        $this->assertEquals($result->count(), 2);

        $lowGroup = $result->findByKey('value', 'low');
        $this->assertEquals($lowGroup->count(), 2);

        $otherGroup = $result->findByKey('value', 'other');
        $this->assertEquals($otherGroup->count(), 1);
    }

    public function testMap()
    {
        $assertValue = [
            ['id' => '#1'],
            ['id' => '#2'],
            ['id' => '#3'],
        ];
        $result = $this->collection->map(function (ModelInterface $model) {
            return [
                'id' => "#{$model['id']}"
            ];
        });

        $this->assertEquals($result, $assertValue);
    }

    public function testRemove()
    {
        $lastModel = null;
        foreach ($this->collection as $model) {
            $lastModel = $model;
        }

        $this->assertCount(3, $this->collection);
        $this->collection->remove($lastModel);
        $this->assertCount(2, $this->collection);
    }

    public function testCount()
    {
        $this->assertCount(3, $this->collection);
        $counter = 0;
        foreach ($this->collection as $model) {
            $counter++;
        }

        $this->assertEquals(3, $counter);
    }

    public function testFind()
    {
        $assertValue = null;
        foreach ($this->collection as $model) {
            if ($model['id'] === 1) {
                $assertValue = $model;
                break;
            }
        }

        $result = $this->collection->find(function (ModelInterface $model) {
            return $model['id'] === 1;
        });

        $this->assertEquals($result, $assertValue);
    }
}
