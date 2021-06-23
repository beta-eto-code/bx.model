<?php

namespace Bx\Model\Models;

use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\ORM\Objectify\State;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\Interfaces\ModelInterface;

class IblockPropertyEnum extends AbsOptimizedModel
{
	/**
	 * @return array
	 */
	protected function toArray(): array
	{
		return [
			"id" => $this->getId(),
			"property_id" => $this->getPropertyId(),
			"value" => $this->getValue(),
			"def" => $this->getDef(),
			"sort" => $this->getSort(),
			"xml_id" => $this->getXmlId(),
			"tmp_id" => $this->getTmpId(),
		];
	}

    /**
     * @param ModelInterface $model
     * @return EntityObject
     */
	public function createElementObjectValue(int $elementId, int $id = null): EntityObject
    {
        $class = "\\Bitrix\\Iblock\\Elements\\EO_IblockProperty{$this->getPropertyId()}";
        /**
         * @var EntityObject $element
         */
        $element = new $class;
        $element['IBLOCK_ELEMENT_ID'] = $elementId;
        $element['IBLOCK_PROPERTY_ID'] = $this->getPropertyId();
        $element['VALUE'] = $this->getId();
        $element['IBLOCK_GENERIC_VALUE'] = (string)$this->getId();

        if ($id) {
            $element['ID'] = $id;
            $element->sysChangeState(State::CHANGED);
        }

        return $element;
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
	 * @return int
	 */
	public function getPropertyId(): int
	{
		return (int)$this["PROPERTY_ID"];
	}


	/**
	 * @param int $value
	 * @return void
	 */
	public function setPropertyId(int $value)
	{
		$this["PROPERTY_ID"] = $value;
	}


	/**
	 * @return string
	 */
	public function getValue(): string
	{
		return (string)$this["VALUE"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setValue(string $value)
	{
		$this["VALUE"] = $value;
	}


	/**
	 * @return string
	 */
	public function getDef(): string
	{
		return (string)$this["DEF"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setDef(string $value)
	{
		$this["DEF"] = $value;
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
}
