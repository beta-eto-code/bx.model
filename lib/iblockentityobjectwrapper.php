<?php

namespace Bx\Model;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Db\SqlQueryException;
use Bitrix\Main\Error;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\Result;
use Bitrix\Main\SystemException;
use Bx\Model\Models\IblockProperty;
use Throwable;

class IblockEntityObjectWrapper
{
    /**
     * @var EntityObject
     */
    private $iblockElementObject;
    /**
     * @var array
     */
    private $lazyUpdate;

    public function __construct(EntityObject $iblockElementObject)
    {
        $this->iblockElementObject = $iblockElementObject;
        $this->lazyUpdate = [];
    }

    /**
     * @return EntityObject
     */
    public function getIblockElementObject(): EntityObject
    {
        return $this->iblockElementObject;
    }

    /**
     * @param IblockProperty $property
     * @param $value
     * @throws SystemException
     * @throws ArgumentException
     */
    public function set(IblockProperty $property, $value)
    {
        if ($property->isMultiple()) {
            $this->lazyUpdate[$property->getCode()] = function () use ($property, $value) {
                $elementId = (int)$this->iblockElementObject->getId();
                if (!$elementId) {
                    return (new Result())->addError(new Error('Element Id is empty'));
                }
                $collection = $property->createEntityObjectValueCollection($elementId, ...(array)$value);
                return $collection->save();
            };
        } else {
            $this->iblockElementObject->set($property->getCode(), $value);
        }
    }

    /**
     * @return Result
     * @throws ArgumentException
     * @throws SystemException
     * @throws SqlQueryException
     */
    public function save(): Result
    {
        $connection = Application::getConnection();
        $connection->startTransaction();

        $result = $this->iblockElementObject->save();
        if (!$result->isSuccess()) {
            return $result;
        }

        foreach ($this->lazyUpdate as $fn) {
            try {
                /**
                 * @var Result $resultSave
                 */
                $resultSave = $fn();
                if (!$resultSave->isSuccess()) {
                    $connection->rollbackTransaction();
                    return $resultSave;
                }
            } catch (Throwable $e) {
                $connection->rollbackTransaction();
                return (new Result())->addError(new Error($e->getMessage()));
            }
        }

        $connection->commitTransaction();
        return $result;
    }
}