<?php

namespace Bx\Model\Interfaces;

use Bx\Model\ModelCollection;

interface AggregateModelInterface extends ModelInterface
{
    /**
     * @param ModelCollection $modelCollection
     * @return AggregateModelInterface
     */
    public static function init(ModelCollection $modelCollection): AggregateModelInterface;
    /**
     * @return ModelCollection
     */
    public function getCollection(): ModelCollection;
}
