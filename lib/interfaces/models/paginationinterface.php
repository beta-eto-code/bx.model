<?php


namespace Bx\Model\Interfaces\Models;


interface PaginationInterface
{
    /**
     * Номер страницы
     * @return int
     */
    public function getPage(): int;

    /**
     * Общее количество страниц
     * @return int
     */
    public function getCountPages(): int;

    /**
     * Общее количество элементов
     * @return int
     */
    public function getTotalCountElements(): int;

    /**
     * Количество элементов на текущей странице
     * @return int
     */
    public function getCountElements(): int;

    /**
     * Максимальное количество элементов на странице
     * @return int
     */
    public function getLimit(): int;

    /**
     * Данные в виде ассициативного массива
     * @return array
     */
    public function toArray(): array;
}
