<?php


namespace Bx\Model\Traits;

use Bitrix\Main\ObjectException;
use Bx\Model\Helper\ExtendedDate;
use Bx\Model\Helper\ExtendedDateTime;

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
            $postValue= '';
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
            } elseif (strpos($key, 'flike_') === 0) {
                $postValue = '%';
                $prefix = '';
                $key = str_replace('flike_', '', $key);
            }

            if (strpos($key, 'date_') === 0) {
                $value = new ExtendedDate($value, 'Y-m-d');
                $key = str_replace('date_', '', $key);
            } elseif (strpos($key, 'datetime_') === 0) {
                $value = new ExtendedDateTime($value, 'Y-m-d\TH:i:s\Z');
                $key = str_replace('datetime_', '', $key);
            }

            $isStrict = false;
            if (strpos($key, 'strict_') === 0) {
                $key = str_replace('strict_', '', $key);
                $isStrict = true;
            }

            if ($this->allowForFilter($key)) {
                if (is_string($key) && isset($filterFields[$key])) {
                    $key = $filterFields[$key];
                }

                if (is_string($value) && !$isStrict) {
                    $valueList = explode(',', $value);
                    $value = count($valueList) > 1 ? $valueList : $value;
                }

                $result[$prefix.$key] = $value.$postValue;
            }
        }

        return $result;
    }
}
