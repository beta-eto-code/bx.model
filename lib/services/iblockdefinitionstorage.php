<?php

namespace Bx\Model\Services;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Error;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\ORM\Objectify\State;
use Bitrix\Main\Result;
use Bitrix\Main\SystemException;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\IblockEntityObjectWrapper;
use Bx\Model\Interfaces\IblockDefinitionStorageServiceInterface;
use Bx\Model\Interfaces\IblockPropertyEnumServiceInterface;
use Bx\Model\Interfaces\IblockPropertyEnumStorageInterface;
use Bx\Model\Interfaces\ModelInterface;
use Bx\Model\Interfaces\ReadableCollectionInterface;
use Bx\Model\Models\IblockDefinition;
use Bx\Model\Models\IblockProperty;
use Bx\Model\Models\IblockPropertyEnum;
use Exception;

class IblockDefinitionStorage implements IblockDefinitionStorageServiceInterface
{

    /**
     * @var IblockDefinitionService
     */
    private $iblockDefinitionService;
    /**
     * @var AbsOptimizedModel|IblockDefinition|null
     */
    private $definition;
    /**
     * @var string
     */
    private $iblockTypeId;
    /**
     * @var string
     */
    private $iblockCode;
    /**
     * @var IblockPropertyEnumStorageInterface
     */
    private $iblockPropertyService;
    /**
     * @var IblockPropertyEnumStorage
     */
    private $iblockEnumStorage;
    /**
     * @var IblockPropertyEnumStorageInterface|IblockPropertyEnumService
     */
    private $iblockPropertyEnumService;

    public function __construct(
        string $iblockTypeId,
        string $iblockCode,
        IblockDefinitionService $iblockDefinitionService = null,
        IblockPropertyService $iblockPropertyService = null,
        IblockPropertyEnumStorageInterface $iblockPropertyEnumService = null
    )
    {
        $this->iblockTypeId = $iblockTypeId;
        $this->iblockCode = $iblockCode;
        $this->iblockDefinitionService = $iblockDefinitionService ?? new IblockDefinitionService();
        $this->iblockPropertyService = new IblockPropertyStorage(
            $this,
            $iblockPropertyService ?? new IblockPropertyService()
        );
        $this->iblockPropertyEnumService = $iblockPropertyEnumService ?? new IblockPropertyEnumService();
        $this->iblockEnumStorage = new IblockPropertyEnumStorage(
            $this,
            $this->iblockPropertyEnumService
        );
    }

    /**
     * @return IblockDefinition|null
     * @throws ArgumentException
     * @throws SystemException
     * @throws ObjectPropertyException
     */
    public function getDefinition(): ?IblockDefinition
    {
        if ($this->definition instanceof IblockDefinition) {
            return $this->definition;
        }

        $definition = $this->iblockDefinitionService
            ->getList([
                'filter' => [
                    '=IBLOCK_TYPE_ID' => $this->iblockTypeId,
                    '=CODE' => $this->iblockCode,
                ],
                'limit' => 1,
            ])->first();

        if (!($definition instanceof IblockDefinition)) {
            return null;
        }

        return $this->definition = $definition;
    }

    /**
     * @return int
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getIblockId(): int
    {
        $definition = $this->getDefinition();
        if (empty($definition)) {
            return 0;
        }

        return $definition->getId();
    }

    /**
     * @param string|null $apiCode
     * @return Result
     * @throws Exception
     */
    public function initApiCodeIfEmpty(string $apiCode = null): Result
    {   $definition = $this->getDefinition();
        if (empty($definition)) {
            return (new Result())->addError(new Error('Инфоблок не найден'));
        }

        if (!$definition->isEmptyApiCode()) {
            return new Result();
        }

        if (empty($apiCode)) {
            $list = explode('_', $this->definition->getIblockTypeId());
            $list = array_merge($list, explode('_', $definition->getCode()));
            $list = array_map(function ($item) {
                return ucfirst($item);
            }, $list);

            $apiCode = implode('', $list);
        }

        $definition->setApiCode($apiCode);
        return $this->iblockDefinitionService->save($definition);
    }

    /**
     * @return DataManager|string|null
     * @throws Exception
     */
    public function getIblockApiTable(): ?string
    {
        if (!$this->initApiCodeIfEmpty()->isSuccess()) {
            return null;
        }

        return "\\Bitrix\\Iblock\\Elements\\Element{$this->getDefinition()->getApiCode()}Table";
    }

    /**
     * @param int|null $id
     * @param ModelInterface|null $model
     * @return IblockEntityObjectWrapper|null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    public function createWrappedEntityObject(int $id = null, ModelInterface $model = null): ?IblockEntityObjectWrapper
    {
        $apiTable = $this->getIblockApiTable();
        if (empty($apiTable)) {
            return null;
        }

        /**
         * @var EntityObject $entityObject
         */
        $entityObject = $apiTable::createObject();
        if (!empty($id) && $id > 0) {
            $entityObject->set('ID', $id);
            $entityObject->sysChangeState(State::CHANGED);
        }

        $wrappedEntityObject = new IblockEntityObjectWrapper($entityObject);
        $propertyList = $this->getPropertiesDefinitionCollection();
        foreach ($model as $name => $value) {
            if ($name === 'ID') {
                continue;
            }

            if (in_array($name, BaseIblockService::getDefaultFields())) {
                $entityObject->set($name, $value);
                continue;
            }

            $clearedName = str_replace('_VALUE', '', $name);
            $property = $propertyList->findByKey('CODE', $clearedName);
            if ($property instanceof IblockProperty && !is_null($value)) {
                $wrappedEntityObject->set($property, $value);
            }
        }

        return $wrappedEntityObject;
    }

    /**
     * @param int|null $id
     * @param ModelInterface|null $model
     * @return EntityObject|null
     * @throws ArgumentException
     * @throws SystemException
     * @throws Exception
     */
    public function createEntityObject(int $id = null, ModelInterface $model = null): ?EntityObject
    {
        $wrappedEntityObject = $this->createWrappedEntityObject($id, $model);
        return $wrappedEntityObject->getIblockElementObject();
    }

    /**
     * @param string $code
     * @param int ...$enumIdList
     * @return IblockPropertyEnum[]|ReadableCollectionInterface
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getEnumCollection(string $code, int ...$enumIdList): ReadableCollectionInterface
    {
        $collection = $this->iblockEnumStorage->getCollectionByCode($code);
        if (empty($enumIdList)) {
            return $collection;
        }

        return $collection->filterByKey('ID', ...$enumIdList);
    }

    /**
     * @param string ...$propertyCodes
     * @return IblockProperty[]|ReadableCollectionInterface
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getPropertiesDefinitionCollection(string ...$propertyCodes): ReadableCollectionInterface
    {
        $collection = $this->iblockPropertyService->getList();
        if (empty($propertyCodes)) {
            return $collection;
        }

        return $collection->filterByKey('CODE', ...$propertyCodes);
    }

    /**
     * @param string $propertyCode
     * @return IblockProperty|null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getPropertyDefinition(string $propertyCode): ?IblockProperty
    {
        return $this->iblockPropertyService->getList()->findByKey('CODE', $propertyCode);
    }

    /**
     * @return IblockPropertyEnumServiceInterface
     */
    public function getIblockPropertyEnumService(): IblockPropertyEnumServiceInterface
    {
        return $this->iblockPropertyEnumService;
    }
}