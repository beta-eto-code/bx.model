<?php


namespace Bx\Model;

use Bx\Model\Interfaces\ModelCollectionInterface;
use Bx\Model\Interfaces\ModelInterface;
use Bx\Model\Interfaces\ModelQueryInterface;
use Bx\Model\Interfaces\ModelServiceInterface;
use Bx\Model\Traits\FilterableHelper;
use Bx\Model\Traits\LimiterHelper;
use Bx\Model\Traits\SortableHelper;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\Interfaces\DerivativeModelInterface;
use Bx\Model\Interfaces\FetcherModelInterface;
use Closure;

abstract class BaseModelService implements ModelServiceInterface
{
    use FilterableHelper;
    use SortableHelper;
    use LimiterHelper;

    /**
     * @var ?Closure
     */
    protected $validateFn = null;

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

    /**
     * @param DerivativeModelInterface|string $class
     * @param array|null $filter
     * @param array|null $sort
     * @param integer|null $limit
     * @param int|null $offset
     * @param array|null $runtime
     * @return ModelCollectionInterface
     */
    public function getModelCollection(
        string $class,
        array $filter = null,
        array $sort = null,
        int $limit = null,
        int $offset = null,
        ?array $runtime = null
    ): ModelCollectionInterface
    {
        $params = [];
        if (!empty($filter)) {
            $params['filter'] = $filter;
        }

        if (!empty($sort)) {
            $params['order'] = $sort;
        }

        if (!empty($limit)) {
            $params['limit'] = $limit;
        }

        if (!empty($offset)) {
            $params['offset'] = $offset;
        }

        if (!empty($runtime)) {
            $params['runtime'] = $runtime;
        }

        $select = $class::getSelect();
        if (!empty($select)) {
            $params['select'] = $select;
        }

        $fetchNamesList = $class::getFetchNamesList();
        if (is_array($fetchNamesList)) {
            $params['fetch'] = $fetchNamesList;
        }
        
        $fetchList = $class::getFetchList();

        /**
         * @var ModelInterface[]|ModelCollection $collection
         */
        $collection = $this->getList($params);
        foreach ($fetchList as $fetcher) {
            if ($fetcher instanceof FetcherModelInterface) {
                $fetcher->fill($collection);
            }
        }

        $newCollection = new ModelCollection([], $class);

        foreach($collection as $model) {
            $newCollection->append($class::init($model));
        }

        return $newCollection;
    }
}
