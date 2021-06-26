<?php

namespace Bx\Model\Models;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ORM\Objectify\Collection;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\ORM\Objectify\State;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\Interfaces\ModelCollectionInterface;
use Bx\Model\ModelCollection;

class IblockProperty extends AbsOptimizedModel
{
	/**
	 * @return array
	 */
	protected function toArray(): array
	{
		return [
			"id" => $this->getId(),
			"timestamp_x" => $this->getTimestampX(),
			"iblock_id" => $this->getIblockId(),
			"name" => $this->getName(),
			"active" => $this->getActive(),
			"sort" => $this->getSort(),
			"code" => $this->getCode(),
			"default_value" => $this->getDefaultValue(),
			"property_type" => $this->getPropertyType(),
			"row_count" => $this->getRowCount(),
			"col_count" => $this->getColCount(),
			"list_type" => $this->getListType(),
			"multiple" => $this->getMultiple(),
			"xml_id" => $this->getXmlId(),
			"file_type" => $this->getFileType(),
			"multiple_cnt" => $this->getMultipleCnt(),
			"tmp_id" => $this->getTmpId(),
			"link_iblock_id" => $this->getLinkIblockId(),
			"with_description" => $this->getWithDescription(),
			"searchable" => $this->getSearchable(),
			"filtrable" => $this->getFiltrable(),
			"is_required" => $this->getIsRequired(),
			"version" => $this->getVersion(),
			"user_type" => $this->getUserType(),
			"user_type_settings" => $this->getUserTypeSettings(),
			"hint" => $this->getHint(),
            "enum_list" => $this->getEnumList(),
		];
	}

    /**
     * @return string
     */
    public function getEntityObjectClass(): string
    {
        return "\\Bitrix\\Iblock\\Elements\\EO_IblockProperty{$this->getId()}";
    }

    /**
     * @return DataManager|string
     */
    public function getEntityObjectTable(): string
    {
        return "\\Bitrix\\Iblock\\Elements\\IblockProperty{$this->getId()}Table";
    }

    /**
     * @return string
     */
    public function getEntityObjectCollectionClass(): string
    {
        return $this->getEntityObjectClass().'_Collection';
    }

    /**
     * @param int $elementId
     * @param ...$values
     * @return Collection|null
     * @throws ArgumentException
     * @throws SystemException
     */
    public function createEntityObjectValueCollection(int $elementId, ...$values): ?Collection
    {
        $class = $this->getEntityObjectCollectionClass();
        /**
         * @var Collection $entityObjectCollection
         */
        $entityObjectCollection = new $class;
        foreach ($values as $value) {
            $entityObjectCollection->add($this->createEntityObjectValue($elementId, $value));
        }

        return $entityObjectCollection;
    }

    /**
     * @param int $elementId
     * @param $value
     * @param int|null $id
     * @return EntityObject
     */
	public function createEntityObjectValue(int $elementId, $value, int $id = null): EntityObject
    {
        $class = $this->getEntityObjectClass();
        $element = new $class;
        $element['IBLOCK_ELEMENT_ID'] = $elementId;
        $element['IBLOCK_PROPERTY_ID'] = $this->getId();
        $element['VALUE'] = $value;
        //$element['IBLOCK_GENERIC_VALUE'] = (string)$value;

        if (!empty($id)) {
            $element['ID'] = $id;
            $element->sysChangeState(State::CHANGED);
        }

        return $element;
    }

    /**
     * @return bool
     */
    public function isEnum(): bool
    {
        return $this->getPropertyType() === PropertyTable::TYPE_LIST;
    }

    /**
     * @return bool
     */
    public function isMultiple(): bool
    {
        return $this->getMultiple() === 'Y';
    }

    /**
     * @param EntityObject $iblockElement
     * @param $value
     * @throws ArgumentException
     * @throws SystemException
     */
    public function setTo(EntityObject $iblockElement, $value)
    {
        if (!$this->isMultiple()) {
            $iblockElement->set($this->getCode(), $value);
        }
    }

    /**
     * @return IblockPropertyEnum[]|ModelCollectionInterface
     */
    public function getEnumList(): ModelCollectionInterface
    {
        $enumList = $this['enum_list'] ?? null;
        if (!$this->isEnum() || !($enumList instanceof ModelCollection)) {
            return new ModelCollection([], IblockPropertyEnum::class);
        }

        return $enumList;
    }

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return (int)$this["ID"];
	}


	/**
	 * @param int $value
	 * @return void
	 */
	public function setId(int $value)
	{
		$this["ID"] = $value;
	}


	/**
	 * @return ?DateTime
	 */
	public function getTimestampX(): ?DateTime
	{
		return $this["TIMESTAMP_X"] instanceof DateTime ? $this["TIMESTAMP_X"] : null;
	}


	/**
	 * @param DateTime $value
	 * @return void
	 */
	public function setTimestampX(DateTime $value)
	{
		$this["TIMESTAMP_X"] = $value;
	}


	/**
	 * @return int
	 */
	public function getIblockId(): int
	{
		return (int)$this["IBLOCK_ID"];
	}


	/**
	 * @param int $value
	 * @return void
	 */
	public function setIblockId(int $value)
	{
		$this["IBLOCK_ID"] = $value;
	}


	/**
	 * @return string
	 */
	public function getName(): string
	{
		return (string)$this["NAME"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setName(string $value)
	{
		$this["NAME"] = $value;
	}


	/**
	 * @return string
	 */
	public function getActive(): string
	{
		return (string)$this["ACTIVE"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setActive(string $value)
	{
		$this["ACTIVE"] = $value;
	}


	/**
	 * @return int
	 */
	public function getSort(): int
	{
		return (int)$this["SORT"];
	}


	/**
	 * @param int $value
	 * @return void
	 */
	public function setSort(int $value)
	{
		$this["SORT"] = $value;
	}


	/**
	 * @return string
	 */
	public function getCode(): string
	{
		return (string)$this["CODE"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setCode(string $value)
	{
		$this["CODE"] = $value;
	}


	/**
	 * @return string
	 */
	public function getDefaultValue(): string
	{
		return (string)$this["DEFAULT_VALUE"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setDefaultValue(string $value)
	{
		$this["DEFAULT_VALUE"] = $value;
	}


	/**
	 * @return string
	 */
	public function getPropertyType(): string
	{
		return (string)$this["PROPERTY_TYPE"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setPropertyType(string $value)
	{
		$this["PROPERTY_TYPE"] = $value;
	}


	/**
	 * @return int
	 */
	public function getRowCount(): int
	{
		return (int)$this["ROW_COUNT"];
	}


	/**
	 * @param int $value
	 * @return void
	 */
	public function setRowCount(int $value)
	{
		$this["ROW_COUNT"] = $value;
	}


	/**
	 * @return int
	 */
	public function getColCount(): int
	{
		return (int)$this["COL_COUNT"];
	}


	/**
	 * @param int $value
	 * @return void
	 */
	public function setColCount(int $value)
	{
		$this["COL_COUNT"] = $value;
	}


	/**
	 * @return string
	 */
	public function getListType(): string
	{
		return (string)$this["LIST_TYPE"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setListType(string $value)
	{
		$this["LIST_TYPE"] = $value;
	}


	/**
	 * @return string
	 */
	public function getMultiple(): string
	{
		return (string)$this["MULTIPLE"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setMultiple(string $value)
	{
		$this["MULTIPLE"] = $value;
	}


	/**
	 * @return string
	 */
	public function getXmlId(): string
	{
		return (string)$this["XML_ID"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setXmlId(string $value)
	{
		$this["XML_ID"] = $value;
	}


	/**
	 * @return string
	 */
	public function getFileType(): string
	{
		return (string)$this["FILE_TYPE"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setFileType(string $value)
	{
		$this["FILE_TYPE"] = $value;
	}


	/**
	 * @return int
	 */
	public function getMultipleCnt(): int
	{
		return (int)$this["MULTIPLE_CNT"];
	}


	/**
	 * @param int $value
	 * @return void
	 */
	public function setMultipleCnt(int $value)
	{
		$this["MULTIPLE_CNT"] = $value;
	}


	/**
	 * @return string
	 */
	public function getTmpId(): string
	{
		return (string)$this["TMP_ID"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setTmpId(string $value)
	{
		$this["TMP_ID"] = $value;
	}


	/**
	 * @return int
	 */
	public function getLinkIblockId(): int
	{
		return (int)$this["LINK_IBLOCK_ID"];
	}


	/**
	 * @param int $value
	 * @return void
	 */
	public function setLinkIblockId(int $value)
	{
		$this["LINK_IBLOCK_ID"] = $value;
	}


	/**
	 * @return string
	 */
	public function getWithDescription(): string
	{
		return (string)$this["WITH_DESCRIPTION"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setWithDescription(string $value)
	{
		$this["WITH_DESCRIPTION"] = $value;
	}


	/**
	 * @return string
	 */
	public function getSearchable(): string
	{
		return (string)$this["SEARCHABLE"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setSearchable(string $value)
	{
		$this["SEARCHABLE"] = $value;
	}


	/**
	 * @return string
	 */
	public function getFiltrable(): string
	{
		return (string)$this["FILTRABLE"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setFiltrable(string $value)
	{
		$this["FILTRABLE"] = $value;
	}


	/**
	 * @return string
	 */
	public function getIsRequired(): string
	{
		return (string)$this["IS_REQUIRED"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setIsRequired(string $value)
	{
		$this["IS_REQUIRED"] = $value;
	}


	/**
	 * @return int
	 */
	public function getVersion(): int
	{
		return (int)$this["VERSION"];
	}


	/**
	 * @param int $value
	 * @return void
	 */
	public function setVersion(int $value)
	{
		$this["VERSION"] = $value;
	}


	/**
	 * @return string
	 */
	public function getUserType(): string
	{
		return (string)$this["USER_TYPE"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setUserType(string $value)
	{
		$this["USER_TYPE"] = $value;
	}


	/**
	 * @return string
	 */
	public function getUserTypeSettings(): string
	{
		return (string)$this["USER_TYPE_SETTINGS"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setUserTypeSettings(string $value)
	{
		$this["USER_TYPE_SETTINGS"] = $value;
	}


	/**
	 * @return string
	 */
	public function getHint(): string
	{
		return (string)$this["HINT"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setHint(string $value)
	{
		$this["HINT"] = $value;
	}
}
