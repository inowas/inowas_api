<?php

namespace Inowas\Flopy\Model\Factory;


use Inowas\Flopy\Model\Adapter\CalculationPropertiesAdapter;
use Inowas\Flopy\Model\Package\CalculationProperties;
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
        $instance->setDateTimeBegin($model->getStart());
        $instance->setDateTimeEnd($model->getEnd());
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
        $instance->setDateTimeBegin($model->getStart());
        $instance->setDateTimeEnd($model->getEnd());

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
        $instance->setDateTimeBegin($model->getStart());
        $instance->setDateTimeEnd($model->getEnd());

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
        $instance->setDateTimeBegin($model->getStart());
        $instance->setDateTimeEnd($model->getEnd());

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
