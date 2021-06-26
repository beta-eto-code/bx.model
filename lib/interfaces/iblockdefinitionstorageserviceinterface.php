<?php

namespace Bx\Model\Interfaces;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\Result;
use Bx\Model\IblockEntityObjectWrapper;
use Bx\Model\Models\IblockProperty;

interface IblockDefinitionStorageServiceInterface
{
    /**
     * @return int
     */
    public function getIblockId(): int;

    /**
     * @param string|null $apiCode
     * @return Result
     */
    public function initApiCodeIfEmpty(string $apiCode = null): Result;

    /**
     * @return DataManager|string|null
     */
    public function getIblockApiTable(): ?string;

    /**
     * @param int|null $id
     * @param ModelInterface|null $model
     * @return IblockEntityObjectWrapper|null
     */
    public function createWrappedEntityObject(int $id = null, ModelInterface $model = null): ?IblockEntityObjectWrapper;

    /**
     * @param int|null $id
     * @param ModelInterface|null $model
     * @return EntityObject|null
     */
    public function createEntityObject(int $id = null, ModelInterface $model = null): ?EntityObject;

    /**
     * @param string $code
     * @param int ...$enumIdList
     * @return ReadableCollectionInterface
     */
    public function getEnumCollection(string $code, int ...$enumIdList): ReadableCollectionInterface;

    /**
     * @param string ...$propertyCodes
     * @return ReadableCollectionInterface
     */
    public function getPropertiesDefinitionCollection(string ...$propertyCodes): ReadableCollectionInterface;

    /**
     * @param string $propertyCode
     * @return IblockProperty|null
     */
    public function getPropertyDefinition(string $propertyCode): ?IblockProperty;

    /**
     * @return IblockPropertyEnumServiceInterface
     */
    public function getIblockPropertyEnumService(): IblockPropertyEnumServiceInterface;
}