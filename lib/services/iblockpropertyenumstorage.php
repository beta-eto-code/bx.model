<?php

namespace Bx\Model\Services;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bx\Model\Interfaces\IblockDefinitionStorageServiceInterface;
use Bx\Model\Interfaces\IblockPropertyEnumServiceInterface;
use Bx\Model\Interfaces\IblockPropertyEnumStorageInterface;
use Bx\Model\Interfaces\ReadableCollectionInterface;
use Bx\Model\Models\IblockPropertyEnum;

class IblockPropertyEnumStorage implements IblockPropertyEnumStorageInterface
{
    /**
     * @var IblockDefinitionStorageServiceInterface
     */
    private $iblockDefinitionStorage;
    /**
     * @var array
     */
    private $enumStorage;
    /**
     * @var IblockPropertyEnumServiceInterface
     */
    private $iblockPropertyEnumService;

    public function __construct(
        IblockDefinitionStorageServiceInterface $iblockDefinitionStorage,
        IblockPropertyEnumServiceInterface $iblockPropertyEnumService = null
    )
    {
        $this->iblockDefinitionStorage = $iblockDefinitionStorage;
        $this->iblockPropertyEnumService = $iblockPropertyEnumService ?? new IblockPropertyEnumService();
        $this->enumStorage = [];
    }

    /**
     * @param string $propertyCode
     * @return IblockPropertyEnum[]|ReadableCollectionInterface
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getCollectionByCode(string $propertyCode): ReadableCollectionInterface
    {
        if (isset($this->enumStorage[$propertyCode])) {
            return $this->enumStorage[$propertyCode];
        }

        return $this->enumStorage[$propertyCode] = $this->iblockPropertyEnumService->getList([
            'filter' => [
                '=PROPERTY.IBLOCK_ID' => $this->iblockDefinitionStorage->getIblockId(),
                '=PROPERTY.CODE' => strtoupper($propertyCode),
            ],
            'order' => [
                'SORT' => 'asc',
            ]
        ]);
    }
}