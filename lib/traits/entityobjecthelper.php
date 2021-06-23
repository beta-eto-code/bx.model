<?php

namespace Bx\Model\Traits;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bx\Model\EntityObjectModel;
use Bx\Model\Interfaces\ModelInterface;
use Bx\Model\ModelCollection;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Iterator;

trait EntityObjectHelper
{
    /**
     * @var ReflectionClass
     */
    protected static $reflectionClass;
    /**
     * @var ReflectionProperty
     */
    protected static $actualValuesProperty;
    /**
     * @var ReflectionProperty
     */
    protected static $currentValuesProperty;
    /**
     * @var ReflectionProperty
     */
    protected static $runtimeValuesProperty;
    /**
     * @var ReflectionProperty
     */
    protected static $customDataProperty;

    /**
     * @throws ReflectionException
     */
    protected function reflectEntityObject()
    {
        if (static::$reflectionClass instanceof ReflectionClass) {
            return;
        }

        static::$reflectionClass = new ReflectionClass(EntityObject::class);
        static::$actualValuesProperty = static::$reflectionClass->getProperty('_actualValues');
        static::$actualValuesProperty->setAccessible(true);

        static::$currentValuesProperty = static::$reflectionClass->getProperty('_currentValues');
        static::$currentValuesProperty->setAccessible(true);

        static::$runtimeValuesProperty = static::$reflectionClass->getProperty('_runtimeValues');
        static::$runtimeValuesProperty->setAccessible(true);

        static::$customDataProperty = static::$reflectionClass->getProperty('_customData');
        static::$customDataProperty->setAccessible(true);
    }

    /**
     * @return array
     */
    protected function getEntityObjectData(): array
    {
        $actualValues = static::$actualValuesProperty->getValue($this->data) ?? [];
        $currentValues = static::$currentValuesProperty->getValue($this->data)?? [];
        $runtimeValues = static::$runtimeValuesProperty->getValue($this->data)?? [];
        $customData = static::$customDataProperty->getValue($this->data)?? [];

        return array_merge($actualValues, $currentValues, $runtimeValues, $customData);
    }

    /**
     * @param $value
     * @param string|null $defaultModelClass
     * @return mixed
     */
    protected function getEntityObjectValue($value, string $defaultModelClass = null)
    {
        if ($value instanceof Iterator) {
            $result = [];
            foreach ($value as $v) {
                $result[] = $this->getEntityObjectValue($v, $defaultModelClass);
            }

            $firstValue = current($result);
            if (count($result) > 0 && is_object($firstValue)) {
                $class = $firstValue instanceof ModelInterface ?
                    get_class($firstValue) :
                    $defaultModelClass ?? EntityObjectModel::class;

                return new ModelCollection($result, $class);
            }

            return $result;
        }

        return $value instanceof EntityObject ?
            (new static($value)) :
            $value;
    }
}