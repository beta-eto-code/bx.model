<?php

namespace Bx\Model\UI;

use Bx\Model\AbsOptimizedModel;
use Closure;

class ConditionalSingleAction extends SingleAction
{
    /**
     * @var Closure
     */
    protected $showConditionCallback;

    /**
     * @param Closure $callback
     * @return ConditionalSingleAction
     */
    public function setShowConditionCallback(Closure $callback): ConditionalSingleAction
    {
        $this->showConditionCallback = $callback;
        return $this;
    }

    /**
     * @param AbsOptimizedModel $model
     * @return bool
     */
    public function isActionAllowedForModel(AbsOptimizedModel $model): bool
    {
        if ($this->showConditionCallback instanceof Closure) {
            $callback = $this->showConditionCallback;
            return (bool)$callback($model);
        }

        return true;
    }
}
