<?php

namespace Bx\Model\Services;


use Bitrix\Main\ArgumentException;
use Bitrix\Main\Db\SqlQueryException;
use Bitrix\Main\Error;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\Result;
use Bitrix\Main\SystemException;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\BaseLinkedModelService;
use Bx\Model\Interfaces\EntityObjectCreatorInterface;
use Bx\Model\Interfaces\IblockDefinitionStorageServiceInterface;
use Bx\Model\Interfaces\ModelInterface;
use Bx\Model\Interfaces\Models\IblockServiceInterface;
use Bx\Model\Interfaces\ReadableCollectionInterface;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\ModelCollection;
use Bx\Model\Models\IblockProperty;
use Exception;

abstract class BaseIblockService extends BaseLinkedModelService implements IblockServiceInterface, EntityObjectCreatorInterface
{
    /**
     * @var IblockDefinitionStorage
     */
    protected $iblockDefinitionStorage;
    /**
     * @var array
     */
    protected $defaultSelect;

    /**
     * @return string
     */
    abstract public function getIblockTypeId(): string;

    /**
     * @return string
     */
    abstract public function getIblockCode(): string;

    /**
     * @return string
     */
    abstract public function getModelClass(): string;

    /**
     * @return IblockDefinitionStorageServiceInterface
     */
    protected function getIblockDefinitionStorage(): IblockDefinitionStorageServiceInterface
    {
        if ($this->iblockDefinitionStorage instanceof IblockDefinitionStorageServiceInterface) {
            return $this->iblockDefinitionStorage;
        }

        return $this->iblockDefinitionStorage = new IblockDefinitionStorage(
            $this->getIblockTypeId(),
            $this->getIblockCode()
        );
    }

    /**
     * @return string[]
     */
    public static function getDefaultFields()
    {
        return [
            "ID",
            "NAME",
            "ACTIVE",
            "IBLOCK_ID",
            "DATE_CREATE",
            "ACTIVE_FROM",
            "ACTIVE_TO",
            "SORT",
            "PREVIEW_PICTURE",
            "PREVIEW_TEXT",
            "DETAIL_PICTURE",
            "DETAIL_TEXT",
            "CODE",
            "TAGS",
            "IBLOCK_SECTION_ID",
            "TIMESTAMP_X",
        ];
    }

    /**
     * @return array|string[]
     */
    protected function getDefaultSelect(): array
    {
        if (!empty($this->defaultSelect)) {
            return $this->defaultSelect;
        }

        $this->defaultSelect = static::getDefaultFields();
        foreach ($this->getPropertiesDefinitionCollection() as $property) {
            $this->defaultSelect["{$property->getCode()}_VALUE"] = "{$property->getCode()}.VALUE";
        }

        return $this->defaultSelect;
    }

    /**
     * @param array $list
     * @return array
     */
    protected function prepareFetchList(array $list): array
    {
        $multiPropList = [];
        foreach ($this->getPropertiesDefinitionCollection() as $property) {
            if ($property->isMultiple()) {
                $multiPropList[] = "{$property->getCode()}_VALUE";
            }
        }

        if (empty($multiPropList)) {
            return $list;
        }

        $result = [];
        $multiValues = [];
        $firstItem = current($list);
        if (!isset($firstItem['ID'])) {
            return $list;
        }

        foreach($list as $item) {
            $key = $item['ID'];
            $result[$key] = $item;

            foreach($multiPropList as $fieldName) {
                $value = $item[$fieldName];
                if (empty($value)) {
                    continue;
                }

                $multiValues[$key][$fieldName] = array_unique(
                    array_merge(
                        (array)($multiValues[$key][$fieldName] ?? []),
                        (array)$value
                    )
                );
            }
        }

        foreach($result as $key => $item) {
            if (empty($multiValues[$key])) {
                continue;
            }

            foreach($multiValues[$key] as $fieldName => $multiValue) {
                $result[$key][$fieldName] = $multiValue;
            }
        }

        return array_values($result);
    }

    /**
     * @param array $params
     * @param UserContextInterface|null $userContext
     * @return ModelCollection
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    protected function getInternalList(array $params, UserContextInterface $userContext = null): ModelCollection
    {
        $params['select'] = $params['select'] ?? $this->getDefaultSelect();
        $table = $this->getIblockDefinitionStorage()->getIblockApiTable();
        $list = $table::getList($params)->fetchAll();
        $list = $this->prepareFetchList($list);

        return new ModelCollection($list, $this->getModelClass());
    }

    /**
     * @return int
     */
    public function getIblockId(): int
    {
        return $this->getIblockDefinitionStorage()->getIblockId();
    }

    /**
     * @param string $code
     * @param int ...$enumIdList
     * @return ReadableCollectionInterface
     */
    public function getEnumCollection(string $code, int ...$enumIdList): ReadableCollectionInterface
    {
        return $this->getIblockDefinitionStorage()->getEnumCollection($code, ...$enumIdList);
    }

    /**
     * @param string ...$propertyCodes
     * @return IblockProperty[]|ReadableCollectionInterface
     */
    public function getPropertiesDefinitionCollection(string ...$propertyCodes): ReadableCollectionInterface
    {
        return $this->getIblockDefinitionStorage()->getPropertiesDefinitionCollection(...$propertyCodes);
    }

    /**
     * @param string $propertyCode
     * @return IblockProperty|null
     */
    public function getPropertyDefinition(string $propertyCode): ?IblockProperty
    {
        return $this->getIblockDefinitionStorage()->getPropertyDefinition($propertyCode);
    }

    /**
     * @param int $id
     * @param ModelInterface $model
     * @return EntityObject
     */
    public function createEntityObject(int $id, ModelInterface $model): EntityObject
    {
        return $this->getIblockDefinitionStorage()->createEntityObject($id, $model);
    }

    /**
     * @param array $params
     * @param UserContextInterface|null $userContext
     * @return int
     * @throws ArgumentException
     * @throws SystemException
     * @throws ObjectPropertyException
     */
    public function getCount(array $params, UserContextInterface $userContext = null): int
    {
        $params['select'] = ['ID'];
        $params['count_total'] = true;
        $table = $this->getIblockDefinitionStorage()->getIblockApiTable();
        return $table::getList($params)->getCount();
    }

    /**
     * @param int $id
     * @param UserContextInterface|null $userContext
     * @return AbsOptimizedModel|null
     */
    public function getById(int $id, UserContextInterface $userContext = null): ?AbsOptimizedModel
    {
        $params = [
            'filter' => [
                '=id' => $id,
            ],
        ];
        $collection = $this->getList($params, $userContext);

        return $collection->first();
    }

    /**
     * @param int $id
     * @param UserContextInterface|null $userContext
     * @return Result
     * @throws Exception
     */
    public function delete(int $id, UserContextInterface $userContext = null): Result
    {
        $item = $this->getById($id, $userContext);
        if (!($item instanceof ModelInterface)) {
            return (new Result)->addError(new Error('Не найдена запись для удаления'));
        }

        $table = $this->getIblockDefinitionStorage()->getIblockApiTable();
        return $table::delete($id);
    }

    /**
     * @param AbsOptimizedModel $model
     * @param UserContextInterface|null $userContext
     * @return Result
     * @throws ArgumentException
     * @throws SqlQueryException
     * @throws SystemException
     */
    public function save(AbsOptimizedModel $model, UserContextInterface $userContext = null): Result
    {
        $id = (int)$model['ID'];
        $wrappedElement = $this->getIblockDefinitionStorage()->createWrappedEntityObject(
            $id,
            $model
        );

        $result = $wrappedElement->save();
        if (!$id) {
            $model['ID'] = (int)$result->getId();
        }

        $this->loadLinkedModel(new ModelCollection([$model], get_class($model)));

        return $result;
    }
 }