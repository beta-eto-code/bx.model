<?php


namespace Bx\Model;

use Bx\Model\Interfaces\FetcherModelInterface;
use Bx\Model\Interfaces\UserContextInterface;

abstract class BaseLinkedModelService extends BaseModelService
{
    /**
     * @return FetcherModelInterface[]
     */
    abstract protected function getLinkedFields(): array;

    abstract protected function getInternalList(array $params, UserContextInterface $userContext = null): ModelCollection;

    final public function getList(array $params, UserContextInterface $userContext = null): ModelCollection
    {
        $fetchList = $params['fetch'] ?? null;
        unset($params['fetch']);

        $collection = $this->getInternalList($params, $userContext);
        $this->loadLinkedModel($collection, $fetchList);

        return $collection;
    }

    /**
     * @param ModelCollection $collection
     * @param array|null $fetchList
     */
    public function loadLinkedModel(ModelCollection $collection, array $fetchList = null)
    {
        $linkedFieldList = $this->getLinkedFields();
        $defaultFetchList = array_keys($linkedFieldList);
        $fetchList = $fetchList ?? $defaultFetchList;

        foreach ($fetchList as $key) {
            $linkedField = $linkedFieldList[$key] ?? null;
            if ($linkedField instanceof FetcherModelInterface) {
                $linkedField->fill($collection);
            }
        }
    }
}