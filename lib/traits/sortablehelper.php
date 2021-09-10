<?php


namespace Bx\Model\Traits;


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
        $keys = array_map(function ($value) {
            return (string)$value;
        }, array_keys($sortFields));

        return in_array($fieldName, $sortFields) || in_array($fieldName, $keys);
    }

    /**
     * @param array $params
     * @return array
     */
    public function getSort(array $params): array
    {
        $sortFields = static::getSortFields();
        $fieldSort = $params['field_sort'] ?? null;
        $orderSort = strtolower($params['order_sort'] ?? '') === 'desc' ? 'desc' : 'asc';

        if (!empty($fieldSort) && static::allowForSort($fieldSort)) {
            if (isset($sortFields[$fieldSort])) {
                $fieldSort = $sortFields[$fieldSort];
            }

            return [$fieldSort => $orderSort];
        }

        return [];
    }
}
