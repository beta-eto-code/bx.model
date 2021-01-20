<?php


namespace Bx\Model\Traits;


trait LimiterHelper
{
    /**
     * Возвращает значение максимального количества элементов выводимого на странице
     * @param array $params
     * @return int
     */
    public function getLimit(array $params): int
    {
        return (int)($params['limit'] ?? 0);
    }

    /**
     * Возвращает номер текущей страницы
     * @param array $params
     * @return int
     */
    public function getPage(array $params): int
    {
        return (int)($params['page'] ?? 1);
    }
}
