<?php

namespace AppBundle\Model;

use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\ChdStressPeriod;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\GhbStressPeriod;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\RchStressPeriod;
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
            return self::createWel();
        }

        if ($type == self::RIV_SP){
            return self::createRiv();
        }

        return new StressPeriod();
    }

    /**
     * @return RivStressPeriod
     */
    public static function createRiv(){
        $sp = new RivStressPeriod();
        $sp->setSteady(false);
        return $sp;
    }

    /**
     * @return WelStressPeriod
     */
    public static function createWel(){
        $sp = new WelStressPeriod();
        $sp->setSteady(false);
        return $sp;
    }

    /**
     * @return RchStressPeriod
     */
    public static function createRch(){
        $sp = new RchStressPeriod();
        $sp->setSteady(false);
        return $sp;
    }

    /**
     * @return ChdStressPeriod
     */
    public static function createChd(){
        $sp = new ChdStressPeriod();
        $sp->setSteady(false);
        return $sp;
    }

    /**
     * @return GhbStressPeriod
     */
    public static function createGhb(){
        $sp = new GhbStressPeriod();
        $sp->setSteady(false);
        return $sp;
    }
}