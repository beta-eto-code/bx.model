<?php

namespace Bx\Model\Interfaces;

use Bx\Model\Interfaces\FetcherModelInterface;

interface DerivativeModelInterface extends ModelInterface
{
    /**
     * @param ModelInterface $model
     * @return DerivativeModelInterface
     */
    public static function init(ModelInterface $model): DerivativeModelInterface;
    /**
     * @return string[]
     */
    public static function getSelect(): array;
    /**
     * @return FetcherModelInterface[]
     */
    public static function getFetchList(): array;
    /**
     * @return string[]|null
     */
    public static function getFetchNamesList(): ?array;
}
