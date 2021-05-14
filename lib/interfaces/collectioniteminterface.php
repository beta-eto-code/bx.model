<?php

namespace Bx\Model\Interfaces;

use JsonSerializable;

interface CollectionItemInterface extends JsonSerializable
{
    /**
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function assertValueByKey(string $key, $value): bool;

    /**
     * @param string $key
     * @return boolean
     */
    public function hasValueKey(string $key): bool;

    /**
     * @param string $key
     * @return mixed
     */
    public function getValueByKey(string $key);
}
