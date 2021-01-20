<?php


namespace Bx\Model\Interfaces\Models;


interface SortableInterface
{
    /**
     * Проверяет разрешено ли сортировать по указанному полю (используется в построителе запроса ModelQueryInterface)
     * @param string $fieldName
     * @return bool
     */
    public function allowForSort(string $fieldName): bool;

    /**
     * Возвращает правило сортировки из переданных данных (используется в построителе запроса ModelQueryInterface)
     * @param array $params
     * @return array
     */
    public function getSort(array $params): array;
}
