<?php

namespace Inowas\ModflowModel\Model\Exception;

use Inowas\Common\Soilmodel\SoilmodelId;

final class SoilmodelNotFoundException extends \InvalidArgumentException
{
    public static function withSoilmodelId(SoilmodelId $id)
    {
        return new self(sprintf('Soilmodel with id %s cannot be found.', $id->toString()));
    }
}
