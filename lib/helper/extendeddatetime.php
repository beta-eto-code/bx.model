<?php


namespace Bx\Model\Helper;


use Bitrix\Main\Type\DateTime;

/**
 * Заглушка для исправления бага в ядре битрикса с вызовом отсутствующего метода compile у DateTime
 */
class ExtendedDateTime extends DateTime
{
    /**
     * @return string
     */
    public function compile(): string
    {
        global $DB;
        return $DB->CharToDateFunction((string)$this, "FULL");
    }
}