<?php


namespace Bx\Model\UI;


use Bx\Model\AbsOptimizedModel;

abstract class BaseGridColumn
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $title;
    /**
     * @var mixed|string
     */
    public $sort;
    /**
     * @var bool
     */
    public $isDefault;

    public function __construct(string $id, string $title, $sort = null, bool $isDefault = true)
    {
        $this->id = $id;
        $this->title = $title;
        $this->sort = $sort ?? $id;
        $this->isDefault = $isDefault;
    }

    /**
     * @param AbsOptimizedModel $model
     * @return mixed|string
     */
    abstract public function getValue(AbsOptimizedModel $model);

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            'sort' => $this->sort,
            'default' => $this->isDefault,
        ];
    }
}