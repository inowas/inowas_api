<?php

namespace Inowas\Flopy\Model\Factory;

use Inowas\Flopy\Model\Adapter\WelPackageAdapter;
use Inowas\Flopy\Model\Package\WelPackage;
use Inowas\ModflowBundle\Model\ModflowModel;
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
