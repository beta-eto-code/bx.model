<?php

namespace Bx\Model\Interfaces;


interface ModelCollectionInterface extends CollectionInterface
{
    /**
     * @param string $key
     * @param string $className
     * @return ModelCollectionInterface
     */
    public function collection(string $key, string $className): ModelCollectionInterface;
}
