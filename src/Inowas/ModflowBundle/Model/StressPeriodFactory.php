<?php

namespace Inowas\ModflowBundle\Model;

use Inowas\ModflowBundle\Model\Boundary\ChdStressPeriod;
use Inowas\ModflowBundle\Model\Boundary\GhbStressPeriod;
use Inowas\ModflowBundle\Model\Boundary\RchStressPeriod;
use Inowas\ModflowBundle\Model\Boundary\RivStressPeriod;
use Inowas\ModflowBundle\Model\Boundary\StressPeriod;
use Inowas\ModflowBundle\Model\Boundary\WelStressPeriod;

class StressPeriodFactory
{
    private final function __construct(){}

    public static function create(){
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

    /**
     * @return RchStressPeriod
     */
    public static function createRch(){
        return new RchStressPeriod();
    }

    /**
     * @return ChdStressPeriod
     */
    public static function createChd(){
        return new ChdStressPeriod();
    }

    /**
     * @return GhbStressPeriod
     */
    public static function createGhb(){
        return new GhbStressPeriod();
    }
}
