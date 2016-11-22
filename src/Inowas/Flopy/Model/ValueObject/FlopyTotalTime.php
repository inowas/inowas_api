<?php

namespace Inowas\Flopy\Model\ValueObject;

use Inowas\Flopy\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\TimeUnit;

class FlopyTotalTime
{
    public static function beginEndToInt(\DateTime $begin, \DateTime $end, TimeUnit $timeUnit){
        $interval = $begin->diff($end);
        return self::intervalToInt($interval, $timeUnit);
    }

    public static function intervalToInt(\DateInterval $interval, TimeUnit $timeUnit){

        switch ($timeUnit->toNative()){
            case TimeUnit::SECOND:
                return $interval->days * 86400;
                break;
            case TimeUnit::MINUTE:
                return $interval->days * 1440;
                break;
            case TimeUnit::DAY:
                return $interval->days;
                break;
            case TimeUnit::YEAR:
                return $interval->y;
                break;
            default:
                throw new InvalidArgumentException(sprintf('Internal unit %s in not matching output-string.', $timeUnit->toNative()));
        }
    }
}
