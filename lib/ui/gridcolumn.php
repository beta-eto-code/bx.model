<?php


namespace Bx\Model\UI;


use Bx\Model\AbsOptimizedModel;

class GridColumn extends BaseGridColumn
{
    /**
     * @param AbsOptimizedModel $model
     * @return mixed|string
     */
    public function getValue(AbsOptimizedModel $model)
    {
        return $model[$this->id] ?? '';
    }
}