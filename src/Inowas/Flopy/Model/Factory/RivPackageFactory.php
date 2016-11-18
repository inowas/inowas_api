<?php

namespace Inowas\FlopyBundle\Model\Factory;

use Inowas\ModflowBundle\Model\Adapter\RivPackageAdapter;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\Package\RivPackage;
use Inowas\SoilmodelBundle\Model\Soilmodel;

class RivPackageFactory implements PackageFactoryInterface
{
    public function create(ModflowModel $model, Soilmodel $soilmodel){

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
