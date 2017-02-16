<?php

namespace Inowas\Modflow\Model\Exception;

use Inowas\Modflow\Model\ModflowId;

final class ModflowCalculationNotFoundException extends \InvalidArgumentException
{
    public static function withId(ModflowId $calculationId)
    {
        return new self(sprintf('ModflowCalculation with id %s cannot be found.', $calculationId->toString()));
    }
}
