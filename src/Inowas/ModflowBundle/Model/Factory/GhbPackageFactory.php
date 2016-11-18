<?php

namespace Inowas\ModflowBundle\Model\Factory;

use Inowas\ModflowBundle\Model\Adapter\GhbPackageAdapter;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\Package\GhbPackage;
use Inowas\SoilmodelBundle\Model\Soilmodel;

class GhbPackageFactory implements PackageFactoryInterface
{
    public function create(ModflowModel $model, Soilmodel $soilmodel){

        $ghb = new GhbPackage();
        $adapter = new GhbPackageAdapter($model);

        $ghb->setIpakcb($adapter->getIpakcb());
        $ghb->setStressPeriodData($adapter->getStressPeriodData());
        $ghb->setDtype($adapter->getDtype());
        $ghb->setExtension($adapter->getExtension());
        $ghb->setUnitnumber($adapter->getUnitnumber());
        $ghb->setOptions($adapter->getOptions());

        return $ghb;
    }
}
