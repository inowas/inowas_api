<?php

namespace Inowas\Flopy\Model\Factory;

use Inowas\Flopy\Model\Adapter\RivPackageAdapter;
use Inowas\Flopy\Model\Package\RivPackage;
use Inowas\ModflowBundle\Model\ModflowModel;
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
