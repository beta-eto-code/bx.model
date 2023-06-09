<?php

namespace Bx\Model\Helper;

use Psr\Http\Message\ServerRequestInterface;

class SortRuleParser
{
    public static function getParsedSortByRequest(ServerRequestInterface $request, array $allowedFields): array
    {
        return static::getParsedSort($request->getQueryParams(), $allowedFields);
    }

    public static function allowForSort(string $fieldName, array $allowedFields): bool
    {
        $keys = array_map(function ($value) {
            return (string)$value;
        }, array_keys($allowedFields));

        return in_array($fieldName, $allowedFields) || in_array($fieldName, $keys);
    }

    public static function getParsedSort(array $params, array $allowedFields): array
    {
        $fieldSort = $params['field_sort'] ?? null;
        $orderSort = strtolower($params['order_sort'] ?? '') === 'desc' ? 'desc' : 'asc';

        if (!empty($fieldSort) && static::allowForSort($fieldSort, $allowedFields)) {
            if (isset($allowedFields[$fieldSort])) {
                $fieldSort = $allowedFields[$fieldSort];
            }

            return [$fieldSort => $orderSort];
        }

        return [];
    }
}
