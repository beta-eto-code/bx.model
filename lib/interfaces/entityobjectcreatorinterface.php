<?php

namespace Bx\Model\Interfaces;

use Bitrix\Main\ORM\Objectify\EntityObject;

interface EntityObjectCreatorInterface
{
    /**
     * @param int $id
     * @param ModelInterface $model
     * @return EntityObject
     */
    public function createEntityObject(int $id, ModelInterface $model): EntityObject;
}