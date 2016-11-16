<?php

namespace Inowas\ModflowBundle\Model\Factory;

use Inowas\ModflowBundle\Model\Adapter\ChdPackageAdapter;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\Package\ChdPackage;
use Inowas\Soilmodel\Model\Soilmodel;

class ChdPackageFactory implements PackageFactoryInterface
{
    public function create(ModflowModel $model, Soilmodel $soilmodel){

        $chd = new ChdPackage();
        $adapter = new ChdPackageAdapter($model);

        $chd->setStressPeriodData($adapter->getStressPeriodData());
        $chd->setDtype($adapter->getDtype());
        $chd->setExtension($adapter->getExtension());
        $chd->setUnitnumber($adapter->getUnitnumber());
        $chd->setOptions($adapter->getOptions());

        return $chd;
    }
}
