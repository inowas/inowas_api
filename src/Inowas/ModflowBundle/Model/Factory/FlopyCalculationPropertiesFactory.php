<?php

namespace Inowas\ModflowBundle\Model\Factory;

use AppBundle\Entity\ModFlowModel;

class FlopyCalculationPropertiesFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){

        $cmd = new FlopyCalculationProperties();
        $adapter = new FlopyCalculationPropertiesAdapter($model);

        $cmd->setPackages($adapter->getPackages());
        $cmd->setWriteInput(true);
        $cmd->setRun(true);

        return $cmd;
    }

    public static function loadFromDiskAndCheck(){
        $instance = new FlopyCalculationProperties();
        $instance->setLoadFrom('nam');
        $instance->setCheck(true);
        $instance->setWriteInput(false);
        $instance->setRun(false);
        $instance->setSubmit(false);

        return $instance;
    }

    public static function loadFromApiWriteAndCheck(ModFlowModel $model){

        $adapter = new FlopyCalculationPropertiesAdapter($model);
        $instance = new FlopyCalculationProperties();
        $instance->setLoadFrom('api');
        $instance->setPackages($adapter->getPackages());
        $instance->setCheck(true);
        $instance->setWriteInput(true);
        $instance->setRun(false);
        $instance->setSubmit(false);

        return $instance;
    }

    public static function loadFromApiAndRun(ModFlowModel $model){

        $adapter = new FlopyCalculationPropertiesAdapter($model);
        $instance = new FlopyCalculationProperties();
        $instance->setLoadFrom('api');
        $instance->setPackages($adapter->getPackages());
        $instance->setInitialValues('ssc');
        $instance->setCheck(false);
        $instance->setWriteInput(true);
        $instance->setRun(true);
        $instance->setSubmit(false);

        return $instance;
    }

    public static function loadFromApiRunAndSubmit(ModFlowModel $model){

        $adapter = new FlopyCalculationPropertiesAdapter($model);
        $instance = new FlopyCalculationProperties();
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
        $instance = new FlopyCalculationProperties();
        $instance->setLoadFrom('nam');
        $instance->setCheck(false);
        $instance->setWriteInput(false);
        $instance->setRun(false);
        $instance->setSubmit(true);
        $instance->setTotim($totim);

        return $instance;
    }
}
