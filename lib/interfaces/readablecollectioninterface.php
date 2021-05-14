<?php

declare(strict_types=1);

namespace Bx\Model\Interfaces;

use IteratorAggregate;
use Countable;
use JsonSerializable;

interface ReadableCollectionInterface extends IteratorAggregate, Countable, JsonSerializable
{
    /**
     * @param string $key
     * @param mixed $value
     * @return CollectionItemInterface|null
     */
    public function findByKey(string $key, $value): ?CollectionItemInterface;
    /**
     * @param callable $fn - attribute CollectionItemInterface
     * @return CollectionItemInterface|null
     */
    public function find(callable $fn): ?CollectionItemInterface;
    /**
     * @param string $key
     * @param string|null $indexKey
     * @param callable|null $fnModifier - attribute is mixed value by the key of the collection item
     * @return array
     */
    public function column(string $key, string $indexKey = null, callable $fnModifier = null): array;
    /**
     * @param string $key
     * @param callable $fn - attribute is mixed value by the key of the collection item
     * @return array
     */
    public function unique(string $key, callable $fnModifier = null): array;
    /**
     * @param string $key
     * @param mixed $value
     * @return CollectionItemInterface[]|CollectionInterface
     */
    public function filterByKey(string $key, $value): CollectionInterface;
    /**
     * @param callable $fn - attribute CollectionItemInterface
     * @return CollectionInterface
     */
    public function filter(callable $fn): CollectionInterface;
    /**
     * @return CollectionItemInterface|null
     */
    public function first(): ?CollectionItemInterface;
}
