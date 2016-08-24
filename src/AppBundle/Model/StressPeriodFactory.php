<?php

namespace AppBundle\Model;

use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\RivStressPeriod;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\WelStressPeriod;

class StressPeriodFactory
{

    const WEL_SP = "WEL";
    const RIV_SP = "RIV";

    private final function __construct(){}

    /**
     * @param null $type
     * @return StressPeriod
     */
    public static function create($type = null)
    {
        if ($type == self::WEL_SP){
            return new WelStressPeriod();
        }

        if ($type == self::RIV_SP){
            return new RivStressPeriod();
        }

        return new StressPeriod();
    }

    /**
     * @return RivStressPeriod
     */
    public static function createRiv(){
        return new RivStressPeriod();
    }

    /**
     * @return WelStressPeriod
     */
    public static function createWel(){
        return new WelStressPeriod();
    }
}