<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;

class RchPackageFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){

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