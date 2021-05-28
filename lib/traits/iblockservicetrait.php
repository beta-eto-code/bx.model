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
}
