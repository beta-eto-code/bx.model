<?php

namespace Bx\Model;

use Bx\Model\Interfaces\CollectionItemInterface;
use Bx\Model\Interfaces\ReadableCollectionInterface;

class MappedCollectionCallback extends MappedCollection
{
    /**
     * @var CollectionItemInterface[]
     */
    protected $list;
    /**
     * @var callable
     */
    private $fn;

    public function __construct($collection, callable $fn)
    {
        $this->list = [];
        $this->fn = $fn;

        foreach($collection as $item) {
            if ($item instanceof CollectionItemInterface) {
                $indexKey = $fn($item);
                $this->list[$indexKey] = $item;
            }
        }
    }

    /**
     * @param callable $fn - attribute CollectionItemInterface
     * @return ReadableCollectionInterface
     */
    public function filter(callable $fn): ReadableCollectionInterface
    {
        return new static((array)array_filter($this->list, $fn), $this->fn);
    }
}
