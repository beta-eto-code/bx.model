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

    /**
     * @param string $id
     * @param string $title
     * @param string|null|false $sort false - отключить сортировку, null - сортировка по полю столбца
     * @param bool $isDefault
     */
    public function __construct(string $id, string $title, $sort = null, bool $isDefault = true)
    {
        $this->id = $id;
        $this->title = $title;
        $this->sort = $sort || $sort === false ? $sort: $id;
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