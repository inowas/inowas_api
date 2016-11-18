<?php

namespace Inowas\ModflowBundle\Model\Factory;

use Inowas\ModflowBundle\Model\Adapter\OcPackageAdapter;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\Package\OcPackage;
use Inowas\SoilmodelBundle\Model\Soilmodel;

class OcPackageFactory implements PackageFactoryInterface
{
    public function create(ModflowModel $model, Soilmodel $soilmodel){

        $oc = new OcPackage();
        $adapter = new OcPackageAdapter($model);

        $oc->setIhedfm($adapter->getIhedfm());
        $oc->setIddnfm($adapter->getIddnfm());
        $oc->setChedfm($adapter->getChedfm());
        $oc->setCddnfm($adapter->getCddnfm());
        $oc->setCboufm($adapter->getCboufm());
        $oc->setCompact($adapter->isCompact());
        $oc->setStressPeriodData($adapter->getStressPeriodData());
        $oc->setExtension($adapter->getExtension());
        $oc->setUnitnumber($adapter->getUnitnumber());

        return $oc;
    }
}
