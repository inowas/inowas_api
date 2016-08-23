<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;

class RivPackageFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){

        $riv = new RivPackage();
        $adapter = new RivPackageAdapter($model);

        $riv->setIpakcb($adapter->getIpakcb());
        $riv->setStressPeriodData($adapter->getStressPeriodData());
        $riv->setDtype($adapter->getDtype());
        $riv->setNaux($adapter->getNaux());
        $riv->setExtension($adapter->getExtension());
        $riv->setUnitnumber($adapter->getUnitnumber());
        $riv->setOptions($adapter->getOptions());

        return $riv;
    }
}