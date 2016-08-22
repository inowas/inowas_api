<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;

class OcPackageFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){

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