<?php

namespace Bx\Model\Interfaces\Models;

use Bx\Model\ModelCollection;
use Bx\Model\Models\IblockPropertyEnum;

interface IblockServiceInterface
{
    /**
     * @return integer
     */
    public function getIblockId(): int;

    /**
     * @param string $code
     * @return IblockPropertyEnum[]|ModelCollection
     */
    public function getEnumCollection(string $code): ModelCollection;
}
