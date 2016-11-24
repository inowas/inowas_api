<?php

namespace Inowas\ModflowBundle\Model;

use Inowas\ModflowBundle\Exception\InvalidArgumentException;
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

    /**
     * @return RchStressPeriod
     */
    public static function createRch(){
        return new RchStressPeriod();
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
     * @param string $type
     * @param array $data
     * @return StressPeriod
     */
    public static function createFromArray(string $type, array $data){
        switch ($type){
            case 'chd':
                return self::createChdFromArray($data);
                break;
            case 'ghb':
                return self::createGhbFromArray($data);
                break;
            case 'rch':
                return self::createRchFromArray($data);
                break;
            case 'riv':
                return self::createRivFromArray($data);
                break;
            case 'wel':
                return self::createWelFromArray($data);
                break;

            default:
                throw new InvalidArgumentException(sprintf('Type %s not known.', $type));

        }
    }

    /**
     * @param array $data
     * @return ChdStressPeriod
     */
    private static function createChdFromArray(array $data){
        $stressPeriod = self::createChd();
        $stressPeriod->setDateTimeBegin(new \DateTime($data[0]));
        $stressPeriod->setShead($data[1]);
        $stressPeriod->setEhead($data[2]);
        return $stressPeriod;
    }

    /**
     * @param array $data
     * @return GhbStressPeriod
     */
    private static function createGhbFromArray(array $data){
        $stressPeriod = self::createGhb();
        $stressPeriod->setDateTimeBegin(new \DateTime($data[0]));
        $stressPeriod->setConductivity($data[1]);
        return $stressPeriod;
    }

    /**
     * @param array $data
     * @return RchStressPeriod
     */
    private static function createRchFromArray(array $data){
        $stressPeriod = self::createRch();
        $stressPeriod->setDateTimeBegin(new \DateTime($data[0]));
        $stressPeriod->setRecharge($data[1]);
        return $stressPeriod;
    }

    /**
     * @param array $data
     * @return RivStressPeriod
     */
    private static function createRivFromArray(array $data){
        $stressPeriod = self::createRiv();
        $stressPeriod->setDateTimeBegin(new \DateTime($data[0]));
        $stressPeriod->setStage($data[1]);
        $stressPeriod->setConductivity($data[2]);
        $stressPeriod->setBottomElevation($data[3]);
        return $stressPeriod;
    }

    /**
     * @param array $data
     * @return WelStressPeriod
     */
    private static function createWelFromArray(array $data){
        $stressPeriod = self::createWel();
        $stressPeriod->setDateTimeBegin(new \DateTime($data[0]));
        $stressPeriod->setFlux($data[1]);
        return $stressPeriod;
    }
}
