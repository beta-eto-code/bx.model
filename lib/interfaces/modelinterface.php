<?php

namespace Bx\Model\Interfaces;

use ArrayAccess;
use IteratorAggregate;

interface ModelInterface extends ArrayAccess, IteratorAggregate, CollectionItemInterface, MappableInterface
{
    /**
     * @return array
     */
    public function getApiModel(): array;
}
