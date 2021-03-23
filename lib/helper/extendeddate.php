<?php


namespace Bx\Model\Helper;


use Bitrix\Main\Type\Date;

class ExtendedDate extends Date
{
    /**
     * @return string
     */
    public function compile(): string
    {
        global $DB;
        return $DB->CharToDateFunction((string)$this, "SHORT");
    }
}