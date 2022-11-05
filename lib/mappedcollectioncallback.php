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

        foreach ($collection as $item) {
            if ($item instanceof CollectionItemInterface) {
                $this->append($item);
            }
        }
    }

    /**
     * @param callable $fn - attribute CollectionItemInterface
     * @return ReadableCollectionInterface
     */
    public function filter(callable $fn): ReadableCollectionInterface
    {
        return new static(array_filter($this->list, $fn), $this->fn);
    }

    /**
     * @param CollectionItemInterface $item
     */
    public function append(CollectionItemInterface $item)
    {
        $indexKey = ($this->fn)($item);
        $this->list[$indexKey] = $item;
    }

    /**
     * @param CollectionItemInterface $item
     */
    public function remove(CollectionItemInterface $item)
    {
        $indexKey = ($this->fn)($item);
        unset($this->list[$indexKey]);
    }
}
