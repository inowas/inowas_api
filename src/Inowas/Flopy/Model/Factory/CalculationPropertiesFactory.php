<?php

namespace Inowas\FlopyBundle\Model\Factory;


use Inowas\FlopyBundle\Model\Adapter\CalculationPropertiesAdapter;
use Inowas\FlopyBundle\Model\Package\CalculationProperties;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\SoilmodelBundle\Model\Soilmodel;

class CalculationPropertiesFactory implements PackageFactoryInterface
{
    public function create(ModflowModel $model, Soilmodel $soilmodel){

        $adapter = new CalculationPropertiesAdapter($model);
        $instance = new CalculationProperties();
        $instance->setPackages($adapter->getPackages());
        $instance->setWriteInput(true);
        $instance->setRun(true);
        return $instance;
    }

    public static function loadFromDiskAndCheck(){
        $instance = new CalculationProperties();
        $instance->setLoadFrom('nam');
        $instance->setCheck(true);
        $instance->setWriteInput(false);
        $instance->setRun(false);
        $instance->setSubmit(false);

        return $instance;
    }

    public static function loadFromApiWriteAndCheck(ModflowModel $model){

        $adapter = new CalculationPropertiesAdapter($model);
        $instance = new CalculationProperties();
        $instance->setLoadFrom('api');
        $instance->setPackages($adapter->getPackages());
        $instance->setCheck(true);
        $instance->setWriteInput(true);
        $instance->setRun(false);
        $instance->setSubmit(false);

        return $instance;
    }

    public static function loadFromApiAndRun(ModflowModel $model){

        $adapter = new CalculationPropertiesAdapter($model);
        $instance = new CalculationProperties();
        $instance->setLoadFrom('api');
        $instance->setPackages($adapter->getPackages());
        $instance->setInitialValues('ssc');
        $instance->setCheck(false);
        $instance->setWriteInput(true);
        $instance->setRun(true);
        $instance->setSubmit(false);

        return $instance;
    }

    public static function loadFromApiRunAndSubmit(ModflowModel $model){

        $adapter = new CalculationPropertiesAdapter($model);
        $instance = new CalculationProperties();
        $instance->setLoadFrom('api');
        $instance->setPackages($adapter->getPackages());
        $instance->setInitialValues('ssc');
        $instance->setCheck(false);
        $instance->setWriteInput(true);
        $instance->setRun(true);
        $instance->setSubmit(true);

        return $instance;
    }

    public static function loadHeads($totim = 0){
        $instance = new CalculationProperties();
        $instance->setLoadFrom('nam');
        $instance->setCheck(false);
        $instance->setWriteInput(false);
        $instance->setRun(false);
        $instance->setSubmit(true);
        $instance->setTotim($totim);

        return $instance;
    }
}
