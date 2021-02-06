<?php


namespace Bx\Model;

use ArrayIterator;
use IteratorAggregate;
use Countable;

class ModelCollection implements IteratorAggregate, Countable
{
    /**
     * @var AbsOptimizedModel[]
     */
    protected $list = [];
    /**
     * @var string
     */
    protected $className;

    public function __construct($list, string $className)
    {
        $this->className = $className;
        foreach ($list as $item) {
            if ($item instanceof $className) {
                $this->list[] = $item;
                continue;
            }

            $this->list[] = new $className($item);
        }
    }

    public function addModel(AbsOptimizedModel $model)
    {
        if ($model instanceof $this->className) {
            $this->list[] = $model;
        }
    }

    public function add(array $modelData)
    {
        $this->list[] = new $this->className($modelData);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->list);
    }

    /**
     * @return AbsOptimizedModel|null
     */
    public function first(): ?AbsOptimizedModel
    {
        $current = current($this->list);
        return $current instanceof AbsOptimizedModel ? $current : null;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->list);
    }

    /**
     * @param string $fieldName
     * @param string|null $keyFieldName
     * @return array
     */
    public function column(string $fieldName, string $keyFieldName = null): array
    {
        $result = [];
        foreach ($this->list as $item) {
            $key = null;
            if (!empty($keyFieldName) && isset($item[$keyFieldName])) {
                $key = $item[$keyFieldName];
            }
            if (isset($item[$fieldName])) {
                if (!empty($key)) {
                    $result[$key] = $item[$fieldName];
                } else {
                    $result[] = $item[$fieldName];
                }
            }
        }

        return $result;
    }

    /**
     * @param string $fieldName
     * @return array
     */
    public function unique(string $fieldName): array
    {
        return array_unique($this->column($fieldName));
    }

    /**
     * @param callable $fn
     * @return array
     */
    public function map(callable $fn): array
    {
        return array_map($fn, $this->list);
    }

    /**
     * @param string $fieldName
     * @param $value
     * @return $this
     */
    public function filerByColumn(string $fieldName, $value): self
    {
        return $this->filter(function (AbsOptimizedModel $item) use ($fieldName, $value) {
            return isset($item[$fieldName]) && $item[$fieldName] == $value;
        });
    }

    /**
     * @param callable $fn
     * @return $this
     */
    public function filter(callable $fn): self
    {
        return new static(array_filter($this->list, $fn), $this->className);
    }

    /**
     * @param string $fieldName
     * @param $value
     * @return AbsOptimizedModel|null
     */
    public function findByColumn(string $fieldName, $value): ?AbsOptimizedModel
    {
        return $this->find(function ($item) use ($fieldName, $value) {
            return isset($item[$fieldName]) && $item[$fieldName] == $value;
        });
    }

    /**
     * @param $fn
     * @return AbsOptimizedModel|null
     */
    public function find($fn): ?AbsOptimizedModel
    {
        foreach ($this->list as $item) {
            if ($fn($item) === true) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param int $index
     * @return AbsOptimizedModel|null
     */
    public function getByIndex(int $index): ?AbsOptimizedModel
    {
        return $this->list[$index] ?? null;
    }

    /**
     * @return array
     */
    public function getApiModel(): array
    {
        return $this->map(function (AbsOptimizedModel $item) {
            return $item->getApiModel();
        });
    }
}
