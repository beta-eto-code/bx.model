<?php

namespace Bx\Model\Helper;

use Bitrix\Main\Type\Date;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

class FilterParser
{
    /**
     * @throws Exception
     */
    public static function getParsedFilterByRequest(ServerRequestInterface $request, array $allowedFields): array
    {
        return static::getParsedFilter($request->getQueryParams(), $allowedFields);
    }

    /**
     * @param array<string,mixed> $filterData
     * @param array $allowedFields
     * @return array
     * @throws Exception
     */
    public static function getParsedFilter(array $filterData, array $allowedFields): array
    {
        $result = [];
        foreach ($filterData as $key => $value) {
            /**
             * @psalm-suppress DocblockTypeContradiction
             */
            if (!is_string($key)) {
                continue;
            }

            [
                $prefix,
                $key,
                $postValue,
                $value,
                $isStrict
            ] = static::parseFilterKeyWithValue($key, $value);
            if (!static::allowForFilter($key, $allowedFields)) {
                continue;
            }

            if (isset($allowedFields[$key])) {
                $key = $allowedFields[$key];
            }

            if (is_string($value) && !$isStrict) {
                $valueList = explode(',', $value);
                $value = count($valueList) > 1 ? $valueList : $value;
            }

            $result[$prefix.$key] = !is_array($value) ?
                $value.$postValue :
                array_map(function($v) use ($postValue) {
                    return $v.$postValue;
                }, $value);
        }

        return $result;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return array
     * @throws Exception
     */
    private static function parseFilterKeyWithValue(string $key, $value): array
    {
        [
            $prefix,
            $key,
            $postValue
        ] = static::parseFilterKey($key);

        if ($value instanceof Date) {
            if (static::isFilterDate($key)) {
                $value = new ExtendedDate($value->format('Y-m-d'), 'Y-m-d');
                $key = str_replace('date_', '', $key);
            } elseif (static::isFilterDateTime($key)) {
                $value = new ExtendedDateTime($value->format('Y-m-d\TH:i:s\Z'), 'Y-m-d\TH:i:s\Z');
                $key = str_replace('datetime_', '', $key);
            }
        }

        $isStrict = static::isStrictFilter($key);
        if ($isStrict) {
            $key = str_replace('strict_', '', $key);
        }

        return [
            $prefix,
           $key,
            $postValue,
            $value,
            $isStrict
        ];
    }

    private static function parseFilterKey(string $key): array
    {
        if (static::isFilterFrom($key)) {
            return static::parseFilterFrom($key);
        }

        if (static::isFilterTo($key)) {
            return static::parseFilterTo($key);
        }

        if (static::isFilterLike($key)) {
            return static::parseFilterLike($key);
        }

        if (static::isFilterFLike($key)) {
            return static::parseFilterFLike($key);
        }

        return [
            '=',
           $key,
            ''
        ];
    }

    private static function isFilterFrom(string $key): bool
    {
        return strpos($key, 'from_') === 0;
    }

    private static function parseFilterFrom(string $key): array
    {
        return [
            '>=',
            str_replace('from_', '', $key),
            ''
        ];
    }

    private static function isFilterTo(string $key): bool
    {
        return strpos($key, 'to_') === 0;
    }

    private static function parseFilterTo(string $key): array
    {
        return [
            '<=',
           str_replace('to_', '', $key),
            ''
        ];
    }

    private static function isFilterLike(string $key): bool
    {
        return strpos($key, 'like_') === 0;
    }

    private static function parseFilterLike(string $key): array
    {
        return [
            '%',
            str_replace('like_', '', $key),
            ''
        ];
    }

    private static function isFilterFLike(string $key): bool
    {
        return strpos($key, 'flike_') === 0;
    }

    private static function parseFilterFLike(string $key): array
    {
        return [
            '',
            str_replace('flike_', '', $key),
            '%'
        ];
    }

    private static function isFilterDate(string $key): bool
    {
        return strpos($key, 'date_') === 0;
    }

    private static function isFilterDateTime(string $key): bool
    {
        return strpos($key, 'datetime_') === 0;
    }

    private static function isStrictFilter(string $key): bool
    {
        return strpos($key, 'strict_') === 0;
    }

    public static function allowForFilter(string $fieldName, array $allowedFields): bool
    {
        $keys = array_map(function ($value) {
            return (string)$value;
        }, array_keys($allowedFields));

        return in_array($fieldName, $allowedFields) || in_array($fieldName, $keys);
    }
}
