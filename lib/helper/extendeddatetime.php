<?php


namespace Bx\Model\Helper;


use Bitrix\Main\Type\DateTime;

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