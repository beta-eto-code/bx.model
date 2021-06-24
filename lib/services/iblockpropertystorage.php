<?php

namespace Bx\Model\Services;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bx\Model\Interfaces\ModelCollectionInterface;
use Bx\Model\Interfaces\Models\IblockServiceInterface;
use Bx\Model\Models\IblockProperty;

class IblockPropertyStorage
{
    /**
     * @var IblockServiceInterface
     */
    private $iblockService;
    /**
     * @var IblockPropertyService
     */
    private $propertyService;
    /**
     * @var ModelCollectionInterface|IblockProperty[]
     */
    private $collection;

    public function __construct(IblockServiceInterface $iblockService, IblockPropertyService $propertyService = null)
    {
        $this->iblockService = $iblockService;
        $this->propertyService = $propertyService ?? new IblockPropertyService();
    }

    /**
     * @return ModelCollectionInterface|IblockProperty[]
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getList(): ModelCollectionInterface
    {
        if ($this->collection instanceof ModelCollectionInterface) {
            return $this->collection;
        }

        $this->collection = $this->propertyService->getList([
            'filter' => [
                '=IBLOCK_ID' => $this->iblockService->getIblockId(),
            ],
        ]);

        foreach ($this->collection as $property) {
            if ($property->isEnum()) {
                $property['enum_list'] = $this->iblockService->getEnumCollection($property->getCode());
            }
        }

        return $this->collection;
    }
}