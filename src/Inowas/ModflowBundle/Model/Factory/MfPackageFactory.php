<?php

namespace Inowas\ModflowBundle\Model\Factory;

use AppBundle\Entity\ModFlowModel;

class MfPackageFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){

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
