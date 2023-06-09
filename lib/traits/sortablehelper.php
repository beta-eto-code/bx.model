<?php


namespace Bx\Model\Traits;

use Bx\Model\Helper\SortRuleParser;

trait SortableHelper
{
    abstract static protected function getSortFields(): array;

    /**
     * @param string $fieldName
     * @return bool
     */
    public function allowForSort(string $fieldName): bool
    {
        $sortFields = static::getSortFields();
        return SortRuleParser::allowForSort($fieldName, $sortFields);
    }

    /**
     * @param array $params
     * @return array
     */
    public function getSort(array $params): array
    {
        $sortFields = static::getSortFields();
        return SortRuleParser::getParsedSort($params, $sortFields);
    }
}
