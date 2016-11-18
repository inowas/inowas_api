<?php

namespace Inowas\ModflowBundle\Model\Factory;

use Inowas\ModflowBundle\Model\Adapter\RchPackageAdapter;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\Package\RchPackage;
use Inowas\SoilmodelBundle\Model\Soilmodel;

class RchPackageFactory implements PackageFactoryInterface
{
    public function create(ModflowModel $model, Soilmodel $soilmodel){

        $rch = new RchPackage();
        $adapter = new RchPackageAdapter($model);

        $rch->setIpakcb($adapter->getIpakcb());
        $rch->setNrchop($adapter->getNrchop());
        $rch->setRech($adapter->getRech());
        $rch->setIrch($adapter->getIrch());
        $rch->setExtension($adapter->getExtension());
        $rch->setUnitnumber($adapter->getUnitnumber());

        return $rch;
    }
}
