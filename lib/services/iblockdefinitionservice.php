<?php

namespace Bx\Model\Services;

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Result;
use Bitrix\Main\SystemException;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\BaseModelService;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\ModelCollection;
use Bx\Model\Models\IblockDefinition;
use Exception;

class IblockDefinitionService extends BaseModelService
{
	/**
	 * @param array $params
	 * @param UserContextInterface|null $userContext
	 * @return IblockDefinition[]|ModelCollection
	 * @throws ArgumentException
	 * @throws ObjectPropertyException
	 * @throws SystemException
	 */
	public function getList(array $params, ?UserContextInterface $userContext = null): ModelCollection
	{
		$params['select'] = $params['select'] ?? [
			"ID",
			"TIMESTAMP_X",
			"IBLOCK_TYPE_ID",
			"LID",
			"CODE",
			"API_CODE",
			"NAME",
			"ACTIVE",
			"SORT",
			"LIST_PAGE_URL",
			"DETAIL_PAGE_URL",
			"SECTION_PAGE_URL",
			"CANONICAL_PAGE_URL",
			"PICTURE",
			"DESCRIPTION",
			"DESCRIPTION_TYPE",
			"XML_ID",
			"TMP_ID",
			"INDEX_ELEMENT",
			"INDEX_SECTION",
			"WORKFLOW",
			"BIZPROC",
			"SECTION_CHOOSER",
			"LIST_MODE",
			"RIGHTS_MODE",
			"SECTION_PROPERTY",
			"PROPERTY_INDEX",
			"VERSION",
			"LAST_CONV_ELEMENT",
			"SOCNET_GROUP_ID",
			"EDIT_FILE_BEFORE",
			"EDIT_FILE_AFTER",
		];
		$list = IblockTable::getList($params);

		return new ModelCollection($list, IblockDefinition::class);
	}


	/**
	 * @param int $id
	 * @param UserContextInterface|null $userContext
	 * @return IblockDefinition|AbsOptimizedModel|null
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
		return IblockTable::getList($params)->getCount();
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
		if (!($item instanceof IblockDefinition)) {
		    return (new Result)->addError(new Error('Не найдена запись для удаления'));
		}

		return IblockTable::delete($id);
	}


	/**
	 * @param IblockDefinition $model
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
		    'TIMESTAMP_X' => [
		        'value' => $model->getTimestampX(),
		        'isFill' => $model->hasValueKey('TIMESTAMP_X'),
		    ],
		    'IBLOCK_TYPE_ID' => [
		        'value' => $model->getIblockTypeId(),
		        'isFill' => $model->hasValueKey('IBLOCK_TYPE_ID'),
		    ],
		    'LID' => [
		        'value' => $model->getLid(),
		        'isFill' => $model->hasValueKey('LID'),
		    ],
		    'CODE' => [
		        'value' => $model->getCode(),
		        'isFill' => $model->hasValueKey('CODE'),
		    ],
		    'API_CODE' => [
		        'value' => $model->getApiCode(),
		        'isFill' => $model->hasValueKey('API_CODE'),
		    ],
		    'NAME' => [
		        'value' => $model->getName(),
		        'isFill' => $model->hasValueKey('NAME'),
		    ],
		    'ACTIVE' => [
		        'value' => $model->getActive(),
		        'isFill' => $model->hasValueKey('ACTIVE'),
		    ],
		    'SORT' => [
		        'value' => $model->getSort(),
		        'isFill' => $model->hasValueKey('SORT'),
		    ],
		    'LIST_PAGE_URL' => [
		        'value' => $model->getListPageUrl(),
		        'isFill' => $model->hasValueKey('LIST_PAGE_URL'),
		    ],
		    'DETAIL_PAGE_URL' => [
		        'value' => $model->getDetailPageUrl(),
		        'isFill' => $model->hasValueKey('DETAIL_PAGE_URL'),
		    ],
		    'SECTION_PAGE_URL' => [
		        'value' => $model->getSectionPageUrl(),
		        'isFill' => $model->hasValueKey('SECTION_PAGE_URL'),
		    ],
		    'CANONICAL_PAGE_URL' => [
		        'value' => $model->getCanonicalPageUrl(),
		        'isFill' => $model->hasValueKey('CANONICAL_PAGE_URL'),
		    ],
		    'PICTURE' => [
		        'value' => $model->getPicture(),
		        'isFill' => $model->hasValueKey('PICTURE'),
		    ],
		    'DESCRIPTION' => [
		        'value' => $model->getDescription(),
		        'isFill' => $model->hasValueKey('DESCRIPTION'),
		    ],
		    'DESCRIPTION_TYPE' => [
		        'value' => $model->getDescriptionType(),
		        'isFill' => $model->hasValueKey('DESCRIPTION_TYPE'),
		    ],
		    'RSS_TTL' => [
		        'value' => $model->getRssTtl(),
		        'isFill' => $model->hasValueKey('RSS_TTL'),
		    ],
		    'RSS_ACTIVE' => [
		        'value' => $model->getRssActive(),
		        'isFill' => $model->hasValueKey('RSS_ACTIVE'),
		    ],
		    'RSS_FILE_ACTIVE' => [
		        'value' => $model->getRssFileActive(),
		        'isFill' => $model->hasValueKey('RSS_FILE_ACTIVE'),
		    ],
		    'RSS_FILE_LIMIT' => [
		        'value' => $model->getRssFileLimit(),
		        'isFill' => $model->hasValueKey('RSS_FILE_LIMIT'),
		    ],
		    'RSS_FILE_DAYS' => [
		        'value' => $model->getRssFileDays(),
		        'isFill' => $model->hasValueKey('RSS_FILE_DAYS'),
		    ],
		    'RSS_YANDEX_ACTIVE' => [
		        'value' => $model->getRssYandexActive(),
		        'isFill' => $model->hasValueKey('RSS_YANDEX_ACTIVE'),
		    ],
		    'XML_ID' => [
		        'value' => $model->getXmlId(),
		        'isFill' => $model->hasValueKey('XML_ID'),
		    ],
		    'TMP_ID' => [
		        'value' => $model->getTmpId(),
		        'isFill' => $model->hasValueKey('TMP_ID'),
		    ],
		    'INDEX_ELEMENT' => [
		        'value' => $model->getIndexElement(),
		        'isFill' => $model->hasValueKey('INDEX_ELEMENT'),
		    ],
		    'INDEX_SECTION' => [
		        'value' => $model->getIndexSection(),
		        'isFill' => $model->hasValueKey('INDEX_SECTION'),
		    ],
		    'WORKFLOW' => [
		        'value' => $model->getWorkflow(),
		        'isFill' => $model->hasValueKey('WORKFLOW'),
		    ],
		    'BIZPROC' => [
		        'value' => $model->getBizproc(),
		        'isFill' => $model->hasValueKey('BIZPROC'),
		    ],
		    'SECTION_CHOOSER' => [
		        'value' => $model->getSectionChooser(),
		        'isFill' => $model->hasValueKey('SECTION_CHOOSER'),
		    ],
		    'LIST_MODE' => [
		        'value' => $model->getListMode(),
		        'isFill' => $model->hasValueKey('LIST_MODE'),
		    ],
		    'RIGHTS_MODE' => [
		        'value' => $model->getRightsMode(),
		        'isFill' => $model->hasValueKey('RIGHTS_MODE'),
		    ],
		    'SECTION_PROPERTY' => [
		        'value' => $model->getSectionProperty(),
		        'isFill' => $model->hasValueKey('SECTION_PROPERTY'),
		    ],
		    'PROPERTY_INDEX' => [
		        'value' => $model->getPropertyIndex(),
		        'isFill' => $model->hasValueKey('PROPERTY_INDEX'),
		    ],
		    'VERSION' => [
		        'value' => $model->getVersion(),
		        'isFill' => $model->hasValueKey('VERSION'),
		    ],
		    'LAST_CONV_ELEMENT' => [
		        'value' => $model->getLastConvElement(),
		        'isFill' => $model->hasValueKey('LAST_CONV_ELEMENT'),
		    ],
		    'SOCNET_GROUP_ID' => [
		        'value' => $model->getSocnetGroupId(),
		        'isFill' => $model->hasValueKey('SOCNET_GROUP_ID'),
		    ],
		    'EDIT_FILE_BEFORE' => [
		        'value' => $model->getEditFileBefore(),
		        'isFill' => $model->hasValueKey('EDIT_FILE_BEFORE'),
		    ],
		    'EDIT_FILE_AFTER' => [
		        'value' => $model->getEditFileAfter(),
		        'isFill' => $model->hasValueKey('EDIT_FILE_AFTER'),
		    ],
		    'SECTIONS_NAME' => [
		        'value' => $model->getSectionsName(),
		        'isFill' => $model->hasValueKey('SECTIONS_NAME'),
		    ],
		    'SECTION_NAME' => [
		        'value' => $model->getSectionName(),
		        'isFill' => $model->hasValueKey('SECTION_NAME'),
		    ],
		    'ELEMENTS_NAME' => [
		        'value' => $model->getElementsName(),
		        'isFill' => $model->hasValueKey('ELEMENTS_NAME'),
		    ],
		    'ELEMENT_NAME' => [
		        'value' => $model->getElementName(),
		        'isFill' => $model->hasValueKey('ELEMENT_NAME'),
		    ],
		];
		$data = [];
		foreach($dataInfo as $name => $info) {
		    if ((bool)$info['isFill']) {
		        $data[$name] = $info['value'];
		    }
		}

		if ($model->getId() > 0) {
		    return IblockTable::update($model->getId(), $data);
		}

		$result = IblockTable::add($data);
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
			"timestamp_x" => "TIMESTAMP_X",
			"iblock_type_id" => "IBLOCK_TYPE_ID",
			"lid" => "LID",
			"code" => "CODE",
			"api_code" => "API_CODE",
			"name" => "NAME",
			"active" => "ACTIVE",
			"sort" => "SORT",
			"list_page_url" => "LIST_PAGE_URL",
			"detail_page_url" => "DETAIL_PAGE_URL",
			"section_page_url" => "SECTION_PAGE_URL",
			"canonical_page_url" => "CANONICAL_PAGE_URL",
			"picture" => "PICTURE",
			"description" => "DESCRIPTION",
			"description_type" => "DESCRIPTION_TYPE",
			"rss_ttl" => "RSS_TTL",
			"rss_active" => "RSS_ACTIVE",
			"rss_file_active" => "RSS_FILE_ACTIVE",
			"rss_file_limit" => "RSS_FILE_LIMIT",
			"rss_file_days" => "RSS_FILE_DAYS",
			"rss_yandex_active" => "RSS_YANDEX_ACTIVE",
			"xml_id" => "XML_ID",
			"tmp_id" => "TMP_ID",
			"index_element" => "INDEX_ELEMENT",
			"index_section" => "INDEX_SECTION",
			"workflow" => "WORKFLOW",
			"bizproc" => "BIZPROC",
			"section_chooser" => "SECTION_CHOOSER",
			"list_mode" => "LIST_MODE",
			"rights_mode" => "RIGHTS_MODE",
			"section_property" => "SECTION_PROPERTY",
			"property_index" => "PROPERTY_INDEX",
			"version" => "VERSION",
			"last_conv_element" => "LAST_CONV_ELEMENT",
			"socnet_group_id" => "SOCNET_GROUP_ID",
			"edit_file_before" => "EDIT_FILE_BEFORE",
			"edit_file_after" => "EDIT_FILE_AFTER",
			"sections_name" => "SECTIONS_NAME",
			"section_name" => "SECTION_NAME",
			"elements_name" => "ELEMENTS_NAME",
			"element_name" => "ELEMENT_NAME",
		];
	}


	/**
	 * @return array
	 */
	public static function getFilterFields(): array
	{
		return [
			"id" => "ID",
			"timestamp_x" => "TIMESTAMP_X",
			"iblock_type_id" => "IBLOCK_TYPE_ID",
			"lid" => "LID",
			"code" => "CODE",
			"api_code" => "API_CODE",
			"name" => "NAME",
			"active" => "ACTIVE",
			"sort" => "SORT",
			"list_page_url" => "LIST_PAGE_URL",
			"detail_page_url" => "DETAIL_PAGE_URL",
			"section_page_url" => "SECTION_PAGE_URL",
			"canonical_page_url" => "CANONICAL_PAGE_URL",
			"picture" => "PICTURE",
			"description" => "DESCRIPTION",
			"description_type" => "DESCRIPTION_TYPE",
			"rss_ttl" => "RSS_TTL",
			"rss_active" => "RSS_ACTIVE",
			"rss_file_active" => "RSS_FILE_ACTIVE",
			"rss_file_limit" => "RSS_FILE_LIMIT",
			"rss_file_days" => "RSS_FILE_DAYS",
			"rss_yandex_active" => "RSS_YANDEX_ACTIVE",
			"xml_id" => "XML_ID",
			"tmp_id" => "TMP_ID",
			"index_element" => "INDEX_ELEMENT",
			"index_section" => "INDEX_SECTION",
			"workflow" => "WORKFLOW",
			"bizproc" => "BIZPROC",
			"section_chooser" => "SECTION_CHOOSER",
			"list_mode" => "LIST_MODE",
			"rights_mode" => "RIGHTS_MODE",
			"section_property" => "SECTION_PROPERTY",
			"property_index" => "PROPERTY_INDEX",
			"version" => "VERSION",
			"last_conv_element" => "LAST_CONV_ELEMENT",
			"socnet_group_id" => "SOCNET_GROUP_ID",
			"edit_file_before" => "EDIT_FILE_BEFORE",
			"edit_file_after" => "EDIT_FILE_AFTER",
			"sections_name" => "SECTIONS_NAME",
			"section_name" => "SECTION_NAME",
			"elements_name" => "ELEMENTS_NAME",
			"element_name" => "ELEMENT_NAME",
		];
	}
}
