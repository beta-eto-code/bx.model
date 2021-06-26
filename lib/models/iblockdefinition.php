<?php

namespace Bx\Model\Models;

use Bitrix\Main\Type\DateTime;
use Bx\Model\AbsOptimizedModel;

class IblockDefinition extends AbsOptimizedModel
{
	/**
	 * @return array
	 */
	protected function toArray(): array
	{
		return [
			"id" => $this->getId(),
			"timestamp_x" => $this->getTimestampX(),
			"iblock_type_id" => $this->getIblockTypeId(),
			"lid" => $this->getLid(),
			"code" => $this->getCode(),
			"api_code" => $this->getApiCode(),
			"name" => $this->getName(),
			"active" => $this->getActive(),
			"sort" => $this->getSort(),
			"list_page_url" => $this->getListPageUrl(),
			"detail_page_url" => $this->getDetailPageUrl(),
			"section_page_url" => $this->getSectionPageUrl(),
			"canonical_page_url" => $this->getCanonicalPageUrl(),
			"picture" => $this->getPicture(),
			"description" => $this->getDescription(),
			"description_type" => $this->getDescriptionType(),
			"xml_id" => $this->getXmlId(),
			"tmp_id" => $this->getTmpId(),
			"index_element" => $this->getIndexElement(),
			"index_section" => $this->getIndexSection(),
			"workflow" => $this->getWorkflow(),
			"bizproc" => $this->getBizproc(),
			"section_chooser" => $this->getSectionChooser(),
			"list_mode" => $this->getListMode(),
			"rights_mode" => $this->getRightsMode(),
			"section_property" => $this->getSectionProperty(),
			"property_index" => $this->getPropertyIndex(),
			"version" => $this->getVersion(),
			"last_conv_element" => $this->getLastConvElement(),
			"socnet_group_id" => $this->getSocnetGroupId(),
			"edit_file_before" => $this->getEditFileBefore(),
			"edit_file_after" => $this->getEditFileAfter(),
		];
	}

    /**
     * @return bool
     */
    public function isEmptyApiCode(): bool
    {
        return empty($this->getApiCode());
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
	 * @return string
	 */
	public function getIblockTypeId(): string
	{
		return (string)$this["IBLOCK_TYPE_ID"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setIblockTypeId(string $value)
	{
		$this["IBLOCK_TYPE_ID"] = $value;
	}


	/**
	 * @return string
	 */
	public function getLid(): string
	{
		return (string)$this["LID"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setLid(string $value)
	{
		$this["LID"] = $value;
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
	public function getApiCode(): string
	{
		return (string)$this["API_CODE"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setApiCode(string $value)
	{
		$this["API_CODE"] = $value;
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
	public function getListPageUrl(): string
	{
		return (string)$this["LIST_PAGE_URL"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setListPageUrl(string $value)
	{
		$this["LIST_PAGE_URL"] = $value;
	}


	/**
	 * @return string
	 */
	public function getDetailPageUrl(): string
	{
		return (string)$this["DETAIL_PAGE_URL"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setDetailPageUrl(string $value)
	{
		$this["DETAIL_PAGE_URL"] = $value;
	}


	/**
	 * @return string
	 */
	public function getSectionPageUrl(): string
	{
		return (string)$this["SECTION_PAGE_URL"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setSectionPageUrl(string $value)
	{
		$this["SECTION_PAGE_URL"] = $value;
	}


	/**
	 * @return string
	 */
	public function getCanonicalPageUrl(): string
	{
		return (string)$this["CANONICAL_PAGE_URL"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setCanonicalPageUrl(string $value)
	{
		$this["CANONICAL_PAGE_URL"] = $value;
	}


	/**
	 * @return int
	 */
	public function getPicture(): int
	{
		return (int)$this["PICTURE"];
	}


	/**
	 * @param int $value
	 * @return void
	 */
	public function setPicture(int $value)
	{
		$this["PICTURE"] = $value;
	}


	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return (string)$this["DESCRIPTION"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setDescription(string $value)
	{
		$this["DESCRIPTION"] = $value;
	}


	/**
	 * @return string
	 */
	public function getDescriptionType(): string
	{
		return (string)$this["DESCRIPTION_TYPE"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setDescriptionType(string $value)
	{
		$this["DESCRIPTION_TYPE"] = $value;
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


	/**
	 * @return string
	 */
	public function getIndexElement(): string
	{
		return (string)$this["INDEX_ELEMENT"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setIndexElement(string $value)
	{
		$this["INDEX_ELEMENT"] = $value;
	}


	/**
	 * @return string
	 */
	public function getIndexSection(): string
	{
		return (string)$this["INDEX_SECTION"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setIndexSection(string $value)
	{
		$this["INDEX_SECTION"] = $value;
	}


	/**
	 * @return string
	 */
	public function getWorkflow(): string
	{
		return (string)$this["WORKFLOW"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setWorkflow(string $value)
	{
		$this["WORKFLOW"] = $value;
	}


	/**
	 * @return string
	 */
	public function getBizproc(): string
	{
		return (string)$this["BIZPROC"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setBizproc(string $value)
	{
		$this["BIZPROC"] = $value;
	}


	/**
	 * @return string
	 */
	public function getSectionChooser(): string
	{
		return (string)$this["SECTION_CHOOSER"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setSectionChooser(string $value)
	{
		$this["SECTION_CHOOSER"] = $value;
	}


	/**
	 * @return string
	 */
	public function getListMode(): string
	{
		return (string)$this["LIST_MODE"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setListMode(string $value)
	{
		$this["LIST_MODE"] = $value;
	}


	/**
	 * @return string
	 */
	public function getRightsMode(): string
	{
		return (string)$this["RIGHTS_MODE"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setRightsMode(string $value)
	{
		$this["RIGHTS_MODE"] = $value;
	}


	/**
	 * @return string
	 */
	public function getSectionProperty(): string
	{
		return (string)$this["SECTION_PROPERTY"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setSectionProperty(string $value)
	{
		$this["SECTION_PROPERTY"] = $value;
	}


	/**
	 * @return string
	 */
	public function getPropertyIndex(): string
	{
		return (string)$this["PROPERTY_INDEX"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setPropertyIndex(string $value)
	{
		$this["PROPERTY_INDEX"] = $value;
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
	 * @return int
	 */
	public function getLastConvElement(): int
	{
		return (int)$this["LAST_CONV_ELEMENT"];
	}


	/**
	 * @param int $value
	 * @return void
	 */
	public function setLastConvElement(int $value)
	{
		$this["LAST_CONV_ELEMENT"] = $value;
	}


	/**
	 * @return int
	 */
	public function getSocnetGroupId(): int
	{
		return (int)$this["SOCNET_GROUP_ID"];
	}


	/**
	 * @param int $value
	 * @return void
	 */
	public function setSocnetGroupId(int $value)
	{
		$this["SOCNET_GROUP_ID"] = $value;
	}


	/**
	 * @return string
	 */
	public function getEditFileBefore(): string
	{
		return (string)$this["EDIT_FILE_BEFORE"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setEditFileBefore(string $value)
	{
		$this["EDIT_FILE_BEFORE"] = $value;
	}


	/**
	 * @return string
	 */
	public function getEditFileAfter(): string
	{
		return (string)$this["EDIT_FILE_AFTER"];
	}


	/**
	 * @param string $value
	 * @return void
	 */
	public function setEditFileAfter(string $value)
	{
		$this["EDIT_FILE_AFTER"] = $value;
	}
}
