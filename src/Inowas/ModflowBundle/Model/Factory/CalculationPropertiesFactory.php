<?php

namespace Inowas\ModflowBundle\Model\Factory;

use Inowas\ModflowBundle\Model\Adapter\CalculationPropertiesAdapter;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\Package\CalculationProperties;
use Inowas\SoilmodelBundle\Model\Soilmodel;

class CalculationPropertiesFactory implements PackageFactoryInterface
{
    public function create(ModflowModel $model, Soilmodel $soilmodel){

        $cmd = new CalculationProperties();
        $adapter = new CalculationPropertiesAdapter($model, $soilmodel);

        $cmd->setPackages($adapter->getPackages());
        $cmd->setWriteInput(true);
        $cmd->setRun(true);

        return $cmd;
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
