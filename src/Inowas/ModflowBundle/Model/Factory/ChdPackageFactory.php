<?php

namespace Inowas\ModflowBundle\Model\Factory;

use AppBundle\Entity\ModFlowModel;

class ChdPackageFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){

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
