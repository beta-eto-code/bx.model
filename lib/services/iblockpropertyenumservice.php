<?php

namespace Bx\Model\Services;

use Bitrix\Iblock\ElementPropertyTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Objectify\Collection;
use Bitrix\Main\Result;
use Bitrix\Main\SystemException;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\BaseModelService;
use Bx\Model\Interfaces\ReadableCollectionInterface;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\ModelCollection;
use Bx\Model\Models\IblockPropertyEnum;
use Bitrix\Iblock\PropertyEnumerationTable;
use Bx\Model\Interfaces\IblockPropertyEnumServiceInterface;
use Bx\Model\Interfaces\Models\IblockServiceInterface;
use Exception;

class IblockPropertyEnumService extends BaseModelService implements IblockPropertyEnumServiceInterface
{
	/**
	 * @param array $params
	 * @param UserContextInterface|null $userContext
	 * @return IblockPropertyEnum[]|ModelCollection
	 * @throws ArgumentException
	 * @throws ObjectPropertyException
	 * @throws SystemException
	 */
	public function getList(array $params, ?UserContextInterface $userContext = null): ModelCollection
	{
		$params['select'] = $params['select'] ?? [
			"ID",
			"PROPERTY_ID",
			"VALUE",
			"DEF",
			"SORT",
			"XML_ID",
			"TMP_ID",
            'PROPERTY_CODE' => 'PROPERTY.CODE',
		];
		$list = PropertyEnumerationTable::getList($params);

		return new ModelCollection($list, IblockPropertyEnum::class);
	}

    /**
     * @param int $propertyId
     * @param int $elementId
     * @return array
     */
	private function getListElementEnumValue(int $propertyId, int $elementId): array
    {
        $result = [];
        $tableClass = "\\Bitrix\\Iblock\\Elements\\IblockProperty{$propertyId}Table";
        $query = $tableClass::getList([
            'filter' => [
                '=IBLOCK_ELEMENT_ID' => $propertyId,
                '=IBLOCK_PROPERTY_ID' => $elementId
            ],
        ]);

        while ($value = $query->fetch()) {
            $enumId = $value['VALUE'];
            $result[$enumId] = $value;
        }

        return $result;
    }

    /**
     * @param IblockServiceInterface $iblockService
     * @param string $propertyCode
     * @return IblockPropertyEnum[]|ModelCollection
     */
    public function getCollectionByCode(IblockServiceInterface $iblockService, string $propertyCode): ModelCollection
	{
		return $this->getList([
			'filter' => [
				'=PROPERTY.IBLOCK_ID' => $iblockService->getIblockId(),
				'=PROPERTY.CODE' => strtoupper($propertyCode),
			],
			'order' => [
				'SORT' => 'asc',
			]
		]);
	}

	/**
	 * @param int $id
	 * @param UserContextInterface|null $userContext
	 * @return IblockPropertyEnum|AbsOptimizedModel|null
	 * @throws ArgumentException
	 * @throws ObjectPropertyException
	 * @throws SystemException
	 */
	public function getById(int $id, ?UserContextInterface $userContext = null): ?AbsOptimizedModel
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
	 * @param array $params
	 * @param UserContextInterface|null $userContext
	 * @return int
	 * @throws ArgumentException
	 * @throws ObjectPropertyException
	 * @throws SystemException
	 */
	public function getCount(array $params, ?UserContextInterface $userContext = null): int
	{
		$params['count_total'] = true;
		return PropertyEnumerationTable::getList($params)->getCount();
	}


	/**
	 * @param int $id
	 * @param UserContextInterface|null $userContext
	 * @return Result
	 * @throws ArgumentException
	 * @throws ObjectPropertyException
	 * @throws SystemException
	 * @throws Exception
	 */
	public function delete(int $id, ?UserContextInterface $userContext = null): Result
	{
		$item = $this->getById($id, $userContext);
		if (!($item instanceof IblockPropertyEnum)) {
		    return (new Result)->addError(new Error('Не найдена запись для удаления'));
		}

		return PropertyEnumerationTable::delete($id);
	}


	/**
	 * @param IblockPropertyEnum $model
	 * @param UserContextInterface|null $userContext
	 * @return Result
	 * @throws Exception
	 */
	public function save(AbsOptimizedModel $model, ?UserContextInterface $userContext = null): Result
	{
		$dataInfo = [
		    'ID' => [
		        'value' => $model->getId(),
		        'isFill' => $model->hasValueKey('ID'),
		    ],
		    'PROPERTY_ID' => [
		        'value' => $model->getPropertyId(),
		        'isFill' => $model->hasValueKey('PROPERTY_ID'),
		    ],
		    'VALUE' => [
		        'value' => $model->getValue(),
		        'isFill' => $model->hasValueKey('VALUE'),
		    ],
		    'DEF' => [
		        'value' => $model->getDef(),
		        'isFill' => $model->hasValueKey('DEF'),
		    ],
		    'SORT' => [
		        'value' => $model->getSort(),
		        'isFill' => $model->hasValueKey('SORT'),
		    ],
		    'XML_ID' => [
		        'value' => $model->getXmlId(),
		        'isFill' => $model->hasValueKey('XML_ID'),
		    ],
		    'TMP_ID' => [
		        'value' => $model->getTmpId(),
		        'isFill' => $model->hasValueKey('TMP_ID'),
		    ],
		];
		$data = [];
		foreach($dataInfo as $name => $info) {
		    if ((bool)$info['isFill']) {
		        $data[$name] = $info['value'];
		    }
		}

		if ($model->getId() > 0) {
		    return PropertyEnumerationTable::update($model->getId(), $data);
		}

		$result = PropertyEnumerationTable::add($data);
		if ($result->isSuccess()) {
		    $model->setId($result->getId());
		}

		return $result;
	}


	/**
	 * @return array
	 */
	public static function getSortFields(): array
	{
		return [
			"id" => "ID",
			"property_id" => "PROPERTY_ID",
			"value" => "VALUE",
			"def" => "DEF",
			"sort" => "SORT",
			"xml_id" => "XML_ID",
			"tmp_id" => "TMP_ID",
		];
	}


	/**
	 * @return array
	 */
	public static function getFilterFields(): array
	{
		return [
			"id" => "ID",
			"property_id" => "PROPERTY_ID",
			"value" => "VALUE",
			"def" => "DEF",
			"sort" => "SORT",
			"xml_id" => "XML_ID",
			"tmp_id" => "TMP_ID",
		];
	}
}
