<?php

namespace Inowas\ModflowBundle\Model\Factory;

use Inowas\ModflowBundle\Model\Adapter\WelPackageAdapter;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\Package\WelPackage;
use Inowas\SoilmodelBundle\Model\Soilmodel;

class WelPackageFactory implements PackageFactoryInterface
{
    public function create(ModflowModel $model, Soilmodel $soilmodel){

        $wel = new WelPackage();
        $adapter = new WelPackageAdapter($model);

        $wel->setIpakcb($adapter->getIpakcb());
        $wel->setStressPeriodData($adapter->getStressPeriodData());
        $wel->setDtype($adapter->getDtype());
        $wel->setExtension($adapter->getExtension());
        $wel->setUnitnumber($adapter->getUnitnumber());
        $wel->setOptions($adapter->getOptions());

        return $wel;
    }
}
