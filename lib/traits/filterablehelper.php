<?php


namespace Bx\Model\Traits;

use Bitrix\Main\ObjectException;
use Bx\Model\Helper\FilterParser;
use Exception;

trait FilterableHelper
{
    abstract protected static function getFilterFields(): array;

    /**
     * @inheritDoc
     */
    public function allowForFilter(string $fieldName): bool
    {
        $filterFields = static::getFilterFields();
        return FilterParser::allowForFilter($fieldName, $filterFields);
    }

    /**
     * @inheritDoc
     * @throws ObjectException
     * @throws Exception
     */
    public function getFilter(array $params): array
    {
        $filterFields = static::getFilterFields();
        $logicOrFields = static::getFilterFieldsWithOrLogic();
        return FilterParser::getParsedFilter($params, $filterFields, $logicOrFields);
    }

    /**
     * Массив групп свойств для фильтрации, которые надо объединять в logic or
     * @return array[]
     */
    protected static function getFilterFieldsWithOrLogic(): array
    {
        return [];
    }
}
