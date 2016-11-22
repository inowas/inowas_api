<?php

namespace Inowas\Flopy\Model\Factory;

use Inowas\Flopy\Model\Adapter\GhbPackageAdapter;
use Inowas\Flopy\Model\Package\GhbPackage;
use Inowas\ModflowBundle\Model\ModflowModel;
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
