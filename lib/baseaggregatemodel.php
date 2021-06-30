<?php

namespace Bx\Model;

use Bx\Model\Interfaces\AggregateModelInterface;
use Exception;

abstract class BaseAggregateModel extends AbsOptimizedModel implements AggregateModelInterface
{
    /**
     * @var ModelCollection
     */
    private $collection;

    /**
     * @param ModelCollection $modelCollection
     * @param array $data
     * @throws Exception
     */
    public function __construct(ModelCollection $modelCollection, array $data = [])
    {
        $this->collection = $modelCollection;
        parent::__construct($data);
    }

    /**
     * @param ModelCollection $modelCollection
     * @return AggregateModelInterface
     * @throws Exception
     */
    public static function init(ModelCollection $modelCollection): AggregateModelInterface
    {
        return new static($modelCollection);
    }

    /**
     * @return ModelCollection
     */
    public function getCollection(): ModelCollection
    {
        return $this->collection;
    }
}
