<?php

namespace Bx\Model\Tests;

use Bx\Model\AbsOptimizedModel;
use Bx\Model\BaseAggregateModel;
use Bx\Model\Interfaces\ModelInterface;
use Bx\Model\ModelCollection;

class BaseAggregateModelTest extends ModelTest
{
    /**
     * @param array $data
     * @param ModelCollection|null $collection
     * @return BaseAggregateModel
     */
    protected function initModel(array $data, ModelCollection $collection = null): ModelInterface
    {
        $collection = $collection ?? new ModelCollection([], AbsOptimizedModel::class);
        return new class($collection, $data) extends BaseAggregateModel {
            protected function toArray(): array
            {
                return $this->data;
            }
        };
    }

    public function testGetCollection()
    {
        $collection = new ModelCollection([], AbsOptimizedModel::class);
        $collection->append($this->initModel([
            'id' => 1,
            'name' => 'name 1',
        ]));
        $collection->append($this->initModel([
            'id' => 2,
            'name' => 'name 2',
        ]));

        $model = $this->initModel([], $collection);
        $this->assertEquals($collection, $model->getCollection());
    }
}
