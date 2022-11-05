<?php

namespace Bx\Model\Interfaces;

interface CollectionInterface extends ReadableCollectionInterface
{
    /**
     * @param CollectionItemInterface $item
     * @return void
     */
    public function append(CollectionItemInterface $item);
    /**
     * @param CollectionItemInterface $item
     * @return void
     */
    public function remove(CollectionItemInterface $item);

    /**
     * @param iterable $list
     * @return ReadableCollectionInterface
     */
    public function newCollection($list): ReadableCollectionInterface;
}
