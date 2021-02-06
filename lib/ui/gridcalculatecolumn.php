<?php


namespace Bx\Model\UI;


use Bx\Model\AbsOptimizedModel;
use Closure;

class GridCalculateColumn extends BaseGridColumn
{
    /**
     * @var Closure
     */
    private $func;

    public function __construct(string $id, Closure $func, string $title, $sort = null, bool $isDefault = true)
    {
        $this->func = $func;
        parent::__construct($id, $title, $sort, $isDefault);
    }

    /**
     * @param AbsOptimizedModel $model
     * @return mixed|string
     */
    public function getValue(AbsOptimizedModel $model)
    {
        $func = $this->func;
        return $func($model) ?? '';
    }
}