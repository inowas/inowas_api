<?php

namespace Inowas\ModflowModel\Model\Exception;

use Inowas\Common\Modflow\TimeUnit;

final class InvalidTimeUnitException extends \InvalidArgumentException
{
    public static function withTimeUnitAndAvailableTimeUnits(TimeUnit $timeUnit, array $availableTimeUnits) {
        return new self(sprintf('The given TimeUnit %s is not valid. Available versions are %s', $timeUnit->toInt(), implode(",",$availableTimeUnits)));
    }
}
