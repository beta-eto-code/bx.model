<?php


namespace Bx\Model\Interfaces;

use Bitrix\Main\ORM\Fields\ExpressionField;
use Bx\Model\ModelCollection;
use Bx\Model\Interfaces\Models\PaginationInterface;

interface ModelQueryInterface extends QueryInterface
{
    /**
     * Форимируем условия фильтра из переданных данных
     * @param array $params
     * @return $this
     */
    public function loadFiler(array $params): self;

    /**
     * Формируем условие сортировки из переданных данных
     * @param array $params
     * @return $this
     */
    public function loadSort(array $params): self;

    /**
     * Формируем параметры для пагинации
     * @param array $params
     * @return $this
     */
    public function loadPagination(array $params): self;
    /**
     * @param string $fieldName
     * @param ExpressionField $expression
     * @return self
     */
    public function setRuntimeField(string $fieldName, ExpressionField $expression): self;
    /**
     * @param string $fieldName
     * @param ExpressionField $expression
     * @return ExpressionField[]
     */
    public function getRuntimeFields(string $fieldName, ExpressionField $expression): array;
    /**
     * @param array $filter
     * @param string|null $prefix
     * @return $this
     */
    public function addFilter(array $filter, string $prefix = null): self;
    /**
     * Возвращает объект пагинации
     * @return PaginationInterface
     */
    public function getPagination(): PaginationInterface;

    /**
     * Возвращает массив с элементами и данными о пагинации
     * @return array
     */
    public function getListPaginationData(): array;

    /**
     * Текущий фильтр
     * @return array
     */
    public function getFilter(): array;

    /**
     * Список элементов
     * @return ModelCollection
     */
    public function getList(): ModelCollection;
}
