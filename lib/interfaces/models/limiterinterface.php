<?php


namespace Bx\Model\Interfaces\Models;


interface LimiterInterface
{
    /**
     * Возвращает значение максимального количества элементов выводимого на странице
     * @param array $params
     * @return int
     */
    public function getLimit(array $params): int;

    /**
     * Возвращает номер текущей страницы
     * @param array $params
     * @return int
     */
    public function getPage(array $params): int;
}
