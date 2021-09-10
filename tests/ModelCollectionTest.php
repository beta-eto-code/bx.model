<?php

namespace Bx\Model\Tests;

use Bx\Model\AbsOptimizedModel;
use Bx\Model\Interfaces\CollectionInterface;
use Bx\Model\ModelCollection;

class ModelCollectionTest extends CollectionTest
{
    /**
     * @param array $data
     * @return ModelCollection
     */
    protected function initCollection(array $data): CollectionInterface
    {
        $collection = new ModelCollection([], AbsOptimizedModel::class);
        foreach ($data as $item) {
            $collection->append($this->initModel($item));
        }

        return $collection;
    }
}