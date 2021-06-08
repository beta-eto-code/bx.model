<?php

namespace Bx\Model\Interfaces;

use Bx\Model\ModelCollection;
use Bx\Model\Interfaces\DerivativeModelInterface;

interface FetcherModelInterface
{
    /**
     * @param ModelCollection $collection
     * @return void
     */
    public function fill(ModelCollection $collection);
    /**
     * @param AggregateModelInterface|string $aggregateModelClass
     * @return FetcherModelInterface
     */
    public function castTo(string $aggregateModelClass): FetcherModelInterface;
    /**
     * @param DerivativeModelInterface|string $derivativeModelClass
     * @return FetcherModelInterface
     */
    public function loadAs(string $derivativeModelClass): FetcherModelInterface;
    /**
     * @param callable $fn
     * @return FetcherModelInterface
     */
    public function setCompareCallback(callable $fn): FetcherModelInterface;
    /**
     * @param callable $fn
     * @return FetcherModelInterface
     */
    public function setModifyCallback(callable $fn): FetcherModelInterface;
}
