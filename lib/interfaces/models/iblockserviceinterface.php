<?php

namespace Bx\Model\Interfaces\Models;

use Bx\Model\Interfaces\ReadableCollectionInterface;
use Bx\Model\Models\IblockProperty;
use Bx\Model\Models\IblockPropertyEnum;

interface IblockServiceInterface
{
    /**
     * @return integer
     */
    public function getIblockId(): int;

    /**
     * @param string $code
     * @param int ...$enumIdList
     * @return IblockPropertyEnum[]|ReadableCollectionInterface
     */
    public function getEnumCollection(string $code, int ...$enumIdList): ReadableCollectionInterface;

    /**
     * @param string ...$propertyCodes
     * @return IblockProperty[]|ReadableCollectionInterface
     */
    public function getPropertiesDefinitionCollection(string ...$propertyCodes): ReadableCollectionInterface;

    /**
     * @param string $propertyCode
     * @return IblockProperty|null
     */
    public function getPropertyDefinition(string $propertyCode): ?IblockProperty;
}
