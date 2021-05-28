<?php

namespace Bx\Model\Models;

use Bx\Model\AbsOptimizedModel;

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
