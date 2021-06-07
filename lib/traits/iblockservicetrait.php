<?php

namespace Bx\Model\Traits;

use Bx\Model\ModelCollection;
use Bx\Model\Interfaces\IblockPropertyEnumServiceInterface;
use Bx\Model\Models\IblockPropertyEnum;
use Bx\Model\Services\IblockPropertyEnumService;

trait IblockServiceTrait
{
    /**
     * @var array
     */
    protected $enumStorage;
    /**
     * @var IblockPropertyEnumServiceInterface
     */
    protected $iblockPropertyEnumService;

    /**
     * @return integer
     */
    abstract public function getIblockId(): int;
    /**
     * @return IblockPropertyEnumServiceInterface
     */
    protected function getIblockPropertyEnumService(): IblockPropertyEnumServiceInterface
    {
        if ($this->iblockPropertyEnumService instanceof IblockPropertyEnumServiceInterface) {
            return $this->iblockPropertyEnumService;
        }

        return $this->iblockPropertyEnumService = new IblockPropertyEnumService(); 
    }

    /**
     * @param string $code
     * @return IblockPropertyEnum[]|ModelCollection
     */
    public function getEnumCollection(string $code): ModelCollection
    {
        if (isset($this->enumStorage[$code]) && $this->enumStorage[$code] instanceof ModelCollection) {
            return $this->enumStorage[$code];
        }
        
        return $this->enumStorage[$code] = $this->getIblockPropertyEnumService()->getCollectionByCode($this, $code);
    }

    /**
     * @param string $idKey
     * @param array $listData
     * @param string ...$multiFieldsName
     * @return array
     */
    protected function prepareFetchList(string $idKey, array $listData, string ...$multiFieldsName): array
    {
        $result = [];
        $multiValues = [];

        $firstItem = current($listData);
        if (!isset($firstItem[$idKey])) {
            return $listData;
        }

        foreach($listData as $item) {
            $key = $item[$idKey];
            $result[$key] = $item;

            foreach($multiFieldsName as $fieldName) {
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
}
