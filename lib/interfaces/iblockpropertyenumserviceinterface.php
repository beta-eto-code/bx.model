<?php

namespace Bx\Model\Interfaces;

use Bx\Model\Interfaces\Models\IblockServiceInterface;
use Bx\Model\ModelCollection;
use Bx\Model\Models\IblockPropertyEnum;

interface IblockPropertyEnumServiceInterface extends ModelServiceInterface
{
    /**
     * @param IblockServiceInterface $iblockService
     * @param string $propertyCode
     * @return IblockPropertyEnum[]|ModelCollection
     */
    public function getCollectionByCode(IblockServiceInterface $iblockService, string $propertyCode): ModelCollection;
}
