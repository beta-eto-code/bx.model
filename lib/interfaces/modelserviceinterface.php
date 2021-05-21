<?php


namespace Bx\Model\Interfaces;

use Bx\Model\Interfaces\Models\QueryableModelServiceInterface;
use Bx\Model\Interfaces\Models\RemoveableModelServiceInterface;
use Bx\Model\Interfaces\Models\SaveableModelServiceInterface;

interface ModelServiceInterface extends QueryableModelServiceInterface, SaveableModelServiceInterface, RemoveableModelServiceInterface
{
}
