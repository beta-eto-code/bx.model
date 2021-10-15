<?php

namespace Bx\Model;

use Bitrix\Main\ORM\Objectify\EntityObject;
use Bx\Model\Interfaces\ModelInterface;
use Bx\Model\Traits\EntityObjectHelper;
use ReflectionException;

class BxModelAdapter
{
    use EntityObjectHelper;

    /**
     * @var EntityObject
     */
    protected $data;

    public function __construct(EntityObject $bxModel)
    {
        $this->data = $bxModel;
    }

    /**
     * @param EntityObject $bxModel
     * @return BxModelAdapter
     */
    public static function init(EntityObject $bxModel): BxModelAdapter
    {
        return new static($bxModel);
    }

    /**
     * @return EntityObject
     */
    public function getBxModel(): EntityObject
    {
        return $this->data;
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function getObjectData(): array
    {
        $this->reflectEntityObject();
        return $this->getEntityObjectData();
    }

    /**
     * @param string|ModelInterface $modelClass
     * @return ModelInterface
     * @throws ReflectionException
     */
    public function getOptimizedModel(string $modelClass): ModelInterface
    {
        return new $modelClass($this->getObjectData());
    }
}