<?php

namespace Bx\Model\Interfaces;

use Bx\Model\Models\IblockPropertyEnum;

interface IblockPropertyEnumStorageInterface
{
    /**
     * @param string $propertyCode
     * @return IblockPropertyEnum[]|ReadableCollectionInterface
     */
    public function getCollectionByCode(string $propertyCode): ReadableCollectionInterface;
}