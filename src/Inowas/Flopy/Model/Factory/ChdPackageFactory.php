<?php

namespace Inowas\Flopy\Model\Factory;

use Inowas\Flopy\Model\Adapter\ChdPackageAdapter;
use Inowas\Flopy\Model\Package\ChdPackage;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\SoilmodelBundle\Model\Soilmodel;

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
