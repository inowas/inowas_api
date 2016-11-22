<?php

namespace Inowas\Flopy\Model\Factory;

use Inowas\Flopy\Model\Adapter\MfPackageAdapter;
use Inowas\Flopy\Model\Package\MfPackage;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\SoilmodelBundle\Model\Soilmodel;

class MfPackageFactory implements PackageFactoryInterface
{
    public function create(ModflowModel $model, Soilmodel $soilmodel){

        $adapter = new MfPackageAdapter($model);
        $mf = new MfPackage();

        $mf->setModelname($adapter->getModelname());
        $mf->setNamefileExt($adapter->getNamefileExt());
        $mf->setVersion($adapter->getVersion());
        $mf->setExeName($adapter->getExeName());
        $mf->setStructured($adapter->isStructured());
        $mf->setListunit($adapter->getListunit());
        $mf->setModelWs($adapter->getModelWs());
        $mf->setExternalPath($adapter->getExternalPath());
        $mf->setVerbose($adapter->isVerbose());
        $mf->setLoad($adapter->isLoad());
        $mf->setSilent($adapter->getSilent());

        return $mf;
    }
}
