<?php

namespace Bx\Model\Services;


use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Result;
use Bitrix\Main\SystemException;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\BaseModelService;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\ModelCollection;
use Bx\Model\Models\IblockProperty;
use Exception;

class IblockPropertyService extends BaseModelService
{
    /**
     * @param array $params
     * @param UserContextInterface|null $userContext
     * @return IblockProperty[]|ModelCollection
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getList(array $params, ?UserContextInterface $userContext = null): ModelCollection
    {
        $params['select'] = $params['select'] ?? [
                "ID",
                "TIMESTAMP_X",
                "IBLOCK_ID",
                "NAME",
                "ACTIVE",
                "SORT",
                "CODE",
                "DEFAULT_VALUE",
                "PROPERTY_TYPE",
                "ROW_COUNT",
                "COL_COUNT",
                "LIST_TYPE",
                "MULTIPLE",
                "XML_ID",
                "FILE_TYPE",
                "MULTIPLE_CNT",
                "TMP_ID",
                "LINK_IBLOCK_ID",
                "WITH_DESCRIPTION",
                "SEARCHABLE",
                "FILTRABLE",
                "IS_REQUIRED",
                "VERSION",
                "USER_TYPE",
                "USER_TYPE_SETTINGS",
                "HINT"
            ];
        $list = PropertyTable::getList($params);

        return new ModelCollection($list, IblockProperty::class);
    }


    /**
     * @param int $id
     * @param UserContextInterface|null $userContext
     * @return IblockProperty|AbsOptimizedModel|null
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
        return PropertyTable::getList($params)->getCount();
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
        if (!($item instanceof IblockProperty)) {
            return (new Result)->addError(new Error('Не найдена запись для удаления'));
        }

        return PropertyTable::delete($id);
    }


    /**
     * @param IblockProperty $model
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
            'IBLOCK_ID' => [
                'value' => $model->getIblockId(),
                'isFill' => $model->hasValueKey('IBLOCK_ID'),
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
            'CODE' => [
                'value' => $model->getCode(),
                'isFill' => $model->hasValueKey('CODE'),
            ],
            'DEFAULT_VALUE' => [
                'value' => $model->getDefaultValue(),
                'isFill' => $model->hasValueKey('DEFAULT_VALUE'),
            ],
            'PROPERTY_TYPE' => [
                'value' => $model->getPropertyType(),
                'isFill' => $model->hasValueKey('PROPERTY_TYPE'),
            ],
            'ROW_COUNT' => [
                'value' => $model->getRowCount(),
                'isFill' => $model->hasValueKey('ROW_COUNT'),
            ],
            'COL_COUNT' => [
                'value' => $model->getColCount(),
                'isFill' => $model->hasValueKey('COL_COUNT'),
            ],
            'LIST_TYPE' => [
                'value' => $model->getListType(),
                'isFill' => $model->hasValueKey('LIST_TYPE'),
            ],
            'MULTIPLE' => [
                'value' => $model->getMultiple(),
                'isFill' => $model->hasValueKey('MULTIPLE'),
            ],
            'XML_ID' => [
                'value' => $model->getXmlId(),
                'isFill' => $model->hasValueKey('XML_ID'),
            ],
            'FILE_TYPE' => [
                'value' => $model->getFileType(),
                'isFill' => $model->hasValueKey('FILE_TYPE'),
            ],
            'MULTIPLE_CNT' => [
                'value' => $model->getMultipleCnt(),
                'isFill' => $model->hasValueKey('MULTIPLE_CNT'),
            ],
            'TMP_ID' => [
                'value' => $model->getTmpId(),
                'isFill' => $model->hasValueKey('TMP_ID'),
            ],
            'LINK_IBLOCK_ID' => [
                'value' => $model->getLinkIblockId(),
                'isFill' => $model->hasValueKey('LINK_IBLOCK_ID'),
            ],
            'WITH_DESCRIPTION' => [
                'value' => $model->getWithDescription(),
                'isFill' => $model->hasValueKey('WITH_DESCRIPTION'),
            ],
            'SEARCHABLE' => [
                'value' => $model->getSearchable(),
                'isFill' => $model->hasValueKey('SEARCHABLE'),
            ],
            'FILTRABLE' => [
                'value' => $model->getFiltrable(),
                'isFill' => $model->hasValueKey('FILTRABLE'),
            ],
            'IS_REQUIRED' => [
                'value' => $model->getIsRequired(),
                'isFill' => $model->hasValueKey('IS_REQUIRED'),
            ],
            'VERSION' => [
                'value' => $model->getVersion(),
                'isFill' => $model->hasValueKey('VERSION'),
            ],
            'USER_TYPE' => [
                'value' => $model->getUserType(),
                'isFill' => $model->hasValueKey('USER_TYPE'),
            ],
            'USER_TYPE_SETTINGS' => [
                'value' => $model->getUserTypeSettings(),
                'isFill' => $model->hasValueKey('USER_TYPE_SETTINGS'),
            ],
            'HINT' => [
                'value' => $model->getHint(),
                'isFill' => $model->hasValueKey('HINT'),
            ],
        ];
        $data = [];
        foreach($dataInfo as $name => $info) {
            if ((bool)$info['isFill']) {
                $data[$name] = $info['value'];
            }
        }

        if ($model->getId() > 0) {
            return PropertyTable::update($model->getId(), $data);
        }

        $result = PropertyTable::add($data);
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
            "iblock_id" => "IBLOCK_ID",
            "name" => "NAME",
            "active" => "ACTIVE",
            "sort" => "SORT",
            "code" => "CODE",
            "default_value" => "DEFAULT_VALUE",
            "property_type" => "PROPERTY_TYPE",
            "row_count" => "ROW_COUNT",
            "col_count" => "COL_COUNT",
            "list_type" => "LIST_TYPE",
            "multiple" => "MULTIPLE",
            "xml_id" => "XML_ID",
            "file_type" => "FILE_TYPE",
            "multiple_cnt" => "MULTIPLE_CNT",
            "tmp_id" => "TMP_ID",
            "link_iblock_id" => "LINK_IBLOCK_ID",
            "with_description" => "WITH_DESCRIPTION",
            "searchable" => "SEARCHABLE",
            "filtrable" => "FILTRABLE",
            "is_required" => "IS_REQUIRED",
            "version" => "VERSION",
            "user_type" => "USER_TYPE",
            "user_type_settings" => "USER_TYPE_SETTINGS",
            "hint" => "HINT",
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
            "iblock_id" => "IBLOCK_ID",
            "name" => "NAME",
            "active" => "ACTIVE",
            "sort" => "SORT",
            "code" => "CODE",
            "default_value" => "DEFAULT_VALUE",
            "property_type" => "PROPERTY_TYPE",
            "row_count" => "ROW_COUNT",
            "col_count" => "COL_COUNT",
            "list_type" => "LIST_TYPE",
            "multiple" => "MULTIPLE",
            "xml_id" => "XML_ID",
            "file_type" => "FILE_TYPE",
            "multiple_cnt" => "MULTIPLE_CNT",
            "tmp_id" => "TMP_ID",
            "link_iblock_id" => "LINK_IBLOCK_ID",
            "with_description" => "WITH_DESCRIPTION",
            "searchable" => "SEARCHABLE",
            "filtrable" => "FILTRABLE",
            "is_required" => "IS_REQUIRED",
            "version" => "VERSION",
            "user_type" => "USER_TYPE",
            "user_type_settings" => "USER_TYPE_SETTINGS",
            "hint" => "HINT",
        ];
    }
}