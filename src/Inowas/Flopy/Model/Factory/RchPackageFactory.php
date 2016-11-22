<?php

namespace Inowas\Flopy\Model\Factory;

use Inowas\Flopy\Model\Adapter\RchPackageAdapter;
use Inowas\Flopy\Model\Package\RchPackage;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\SoilmodelBundle\Model\Soilmodel;

class RchPackageFactory implements PackageFactoryInterface
{
    public function create(ModflowModel $model, Soilmodel $soilmodel){

        $rch = new RchPackage();
        $adapter = new RchPackageAdapter($model);

        $rch->setIpakcb($adapter->getIpakcb());
        $rch->setNrchop($adapter->getNrchop());
        $rch->setRech($adapter->getStressPeriodData());
        $rch->setIrch($adapter->getIrch());
        $rch->setExtension($adapter->getExtension());
        $rch->setUnitnumber($adapter->getUnitnumber());

        return $rch;
    }
}
