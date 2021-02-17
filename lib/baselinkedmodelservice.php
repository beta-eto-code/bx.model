<?php


namespace Bx\Model;


use Bx\Model\Interfaces\UserContextInterface;

abstract class BaseLinkedModelService extends BaseModelService
{
    /**
     * @return FetcherModel[]
     */
    abstract protected function getLinkedFields(): array;

    abstract protected function getInternalList(array $params, UserContextInterface $userContext = null): ModelCollection;

    final public function getList(array $params, UserContextInterface $userContext = null): ModelCollection
    {
        $linkedFieldList = $this->getLinkedFields();
        $defaultFetchList = array_keys($linkedFieldList);
        $fetchList = $params['fetch'] ?? $defaultFetchList;
        unset($params['fetch']);

        $collection = $this->getInternalList($params, $userContext);
        foreach ($fetchList as $key) {
            $linkedField = $linkedFieldList[$key] ?? null;
            if ($linkedField instanceof FetcherModel) {
                $linkedField->fill($collection);
            }
        }

        return $collection;
    }
}