<?php

namespace Inowas\ModflowBundle\Model\Factory;

use AppBundle\Entity\ModFlowModel;

class WelPackageFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){

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
