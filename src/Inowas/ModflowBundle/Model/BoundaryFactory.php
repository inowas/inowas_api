<?php

namespace Inowas\ModflowBundle\Model;

use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\Boundary\ConstantHeadBoundary;
use Inowas\ModflowBundle\Model\Boundary\GeneralHeadBoundary;
use Inowas\ModflowBundle\Model\Boundary\RechargeBoundary;
use Inowas\ModflowBundle\Model\Boundary\RiverBoundary;
use Inowas\ModflowBundle\Model\Boundary\WellBoundary;

class BoundaryFactory
{
    private final function __construct(){}

    public static function create(string $type){
        switch (strtolower($type)){
            case 'chd':
                return self::createChd();
                break;
            case 'ghb':
                return self::createGhb();
                break;
            case 'rch':
                return self::createRch();
                break;
            case 'riv':
                return self::createRiv();
                break;
            case 'wel':
                return self::createWel();
                break;
            default:
                throw new InvalidArgumentException(sprintf('Boundary type %s not known', $type));
        }
    }

    /**
     * @return ConstantHeadBoundary
     */
    public static function createChd()
    {
        return new ConstantHeadBoundary();
    }

    /**
     * @return GeneralHeadBoundary
     */
    public static function createGhb()
    {
        return new GeneralHeadBoundary();
    }

    /**
     * @return RechargeBoundary
     */
    public static function createRch()
    {
        return new RechargeBoundary();
    }

    /**
     * @return RiverBoundary
     */
    public static function createRiv()
    {
        return new RiverBoundary();
    }

    /**
     * @return WellBoundary
     */
    public static function createWel()
    {
        return new WellBoundary();
    }
}

