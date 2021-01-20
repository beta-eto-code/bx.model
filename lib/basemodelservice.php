<?php


namespace Bx\Model;


use Bx\Model\Interfaces\ModelQueryInterface;
use Bx\Model\Interfaces\ModelServiceInterface;
use Bx\Model\Traits\FilterableHelper;
use Bx\Model\Traits\LimiterHelper;
use Bx\Model\Traits\SortableHelper;
use Bx\Model\Interfaces\UserContextInterface;
use Closure;

abstract class BaseModelService implements ModelServiceInterface
{
    use FilterableHelper;
    use SortableHelper;
    use LimiterHelper;

    /**
     * @var Closure
     */
    protected $validateFn;

    /**
     * @inheritDoc
     */
    public function query(UserContextInterface $userContext = null): ModelQueryInterface
    {
        return new QueryModel($this, $userContext);
    }

    public function extendLogic(Closure $fn)
    {
        $this->validateFn = $fn;
    }
}
