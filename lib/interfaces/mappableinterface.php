<?php

namespace Bx\Model\Interfaces;

interface MappableInterface
{
    /**
     * @param callable $fnMap - function($item): array
     * @return mixed
     */
    public function map(callable $fnMap);
}