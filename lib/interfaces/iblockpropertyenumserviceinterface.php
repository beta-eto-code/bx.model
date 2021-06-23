<?php

namespace Bx\Model\Interfaces;

use Bitrix\Main\ORM\Objectify\Collection;
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

    /**
     * @param int $elementId
     * @param ReadableCollectionInterface $enumCollection
     * @param string|null $propertyCode
     * @return Collection|null
     */
    public function createCollectionEnumValue(
        int $elementId,
        ReadableCollectionInterface $enumCollection,
        string $propertyCode = null
    ): ?Collection;
}
