<?php


namespace Bx\Model\Traits;

use Bitrix\Main\ObjectException;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;

trait FilterableHelper
{
    abstract static protected function getFilterFields(): array;

    /**
     * @inheritDoc
     */
    public function allowForFilter(string $fieldName): bool
    {
        $filterFields = static::getFilterFields();
        $keys = array_map(function ($value) {
            return (string)$value;
        }, array_keys($filterFields));

        return in_array($fieldName, $filterFields) || in_array($fieldName, $keys);
    }

    /**
     * @inheritDoc
     * @throws ObjectException
     */
    public function getFilter(array $params): array
    {
        $result = [];
        $filterFields = static::getFilterFields();
        foreach ($params as $key => $value) {
            $prefix = '=';
            if (strpos($key, 'from_') === 0) {
                $prefix = '>=';
                $key = str_replace('from_', '', $key);
            } elseif (strpos($key, 'to_') === 0) {
                $prefix = '<=';
                $key = str_replace('to_', '', $key);
            } elseif (strpos($key, 'like_') === 0) {
                $prefix = '%';
                $key = str_replace('like_', '', $key);
            }

            if (strpos('date_', $key) === 0) {
                $value = new Date($value, 'Y-m-d');
                $key = str_replace('date_', '', $key);
            } elseif (strpos('datetime_', $key) === 0) {
                $value = new DateTime($value, 'Y-m-d\TH:i:s\Z');
                $key = str_replace('datetime_', '', $key);
            }

            if ($this->allowForFilter($key)) {
                if (is_string($key) && isset($filterFields[$key])) {
                    $key = $filterFields[$key];
                }

                $result[$prefix.$key] = explode(',', $value);
            }
        }

        return $result;
    }
}
