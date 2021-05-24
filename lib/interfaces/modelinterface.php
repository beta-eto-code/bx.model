<?php

namespace Bx\Model\Interfaces;

use ArrayAccess;
use IteratorAggregate;

interface ModelInterface extends ArrayAccess, IteratorAggregate, CollectionItemInterface
{
    /**
     * @return array
     */
    public function getApiModel(): array;
}
