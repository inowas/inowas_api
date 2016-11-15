<?php

namespace Inowas\ModflowBundle\Model\Factory;

use AppBundle\Entity\ModFlowModel;

class GhbPackageFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){

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
