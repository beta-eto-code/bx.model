<?php

namespace Bx\Model\Interfaces\Models;

use Bitrix\Main\ORM\Objectify\Collection;
use Bx\Model\Interfaces\ReadableCollectionInterface;
use Bx\Model\Models\IblockPropertyEnum;

interface IblockServiceInterface
{
    /**
     * @return integer
     */
    public function getIblockId(): int;

    /**
     * @param string $code
     * @param int ...$enumIdList
     * @return IblockPropertyEnum[]|ReadableCollectionInterface
     */
    public function getEnumCollection(string $code, int ...$enumIdList): ReadableCollectionInterface;

    /**
     * @param string $code
     * @param int $elementId
     * @param int ...$enumIdList
     * @return Collection|null
     */
    public function createCollectionEnumValue(string $code, int $elementId, int ...$enumIdList): ?Collection;
}
