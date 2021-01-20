<?php


namespace Bx\Model\Interfaces\Models;

interface FilterableInterface
{
    /**
     * Проверяет разрешено ли фитьтровать элементы по указаному полю (используется в построителе запроса ModelQueryInterface)
     * @param string $fieldName
     * @return bool
     */
    public function allowForFilter(string $fieldName): bool;

    /**
     * Возвращает фильтр собранный из переданных данных (используется в построителе запросов ModelQueryInterface)
     * @param array $params
     * @return array
     */
    public function getFilter(array $params): array;
}
