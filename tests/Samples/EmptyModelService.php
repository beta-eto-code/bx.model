<?php

namespace Bx\Model\Tests\Samples;

use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\BaseModelService;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\ModelCollection;

class EmptyModelService extends BaseModelService
{
    /**
     * @var string[]
     */
    public static $allowedFilterFields = ['id', 'name'];
    /**
     * @var string[]
     */
    public static $allowedSortFields = ['id', 'name'];

    /**
     * @var ModelCollection|null
     */
    public $resultList;

    /**
     * @var AbsOptimizedModel|null
     */
    public $resultItem;

    /**
     * @var int
     */
    public $resultCount = 0;

    public function __construct()
    {
        $this->resultList = new ModelCollection([], '');
    }

    static protected function getFilterFields(): array
    {
        return static::$allowedFilterFields;
    }

    public function getList(array $params, UserContextInterface $userContext = null): ModelCollection
    {
        return $this->resultList instanceof ModelCollection ? $this->resultList : new ModelCollection([], '');
    }

    public function getCount(array $params, UserContextInterface $userContext = null): int
    {
        return (int)$this->resultCount;
    }

    public function getById(int $id, UserContextInterface $userContext = null): ?AbsOptimizedModel
    {
        return $this->resultItem instanceof AbsOptimizedModel ? $this->resultItem : null;
    }

    public function delete(int $id, UserContextInterface $userContext = null): Result
    {
        return (new Result())->addError(new Error('Not implemented'));
    }

    public function save(AbsOptimizedModel $model, UserContextInterface $userContext = null): Result
    {
        return (new Result())->addError(new Error('Not implemented'));
    }

    static protected function getSortFields(): array
    {
        return static::$allowedSortFields;
    }
}