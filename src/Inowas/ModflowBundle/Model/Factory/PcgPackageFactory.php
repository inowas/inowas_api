<?php

namespace Inowas\ModflowBundle\Model\Factory;

use Inowas\ModflowBundle\Model\Adapter\PcgPackageAdapter;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\Package\PcgPackage;
use Inowas\SoilmodelBundle\Model\Soilmodel;

class PcgPackageFactory implements PackageFactoryInterface
{
    public function create(ModflowModel $model, Soilmodel $soilmodel){

        $pcg = new PcgPackage();
        $adapter = new PcgPackageAdapter($model);

        $pcg->setMxiter($adapter->getMxiter());
        $pcg->setIter1($adapter->getIter1());
        $pcg->setNpcond($adapter->getNpcond());
        $pcg->setHclose($adapter->getHclose());
        $pcg->setRclose($adapter->getRclose());
        $pcg->setRelax($adapter->getRelax());
        $pcg->setNbpol($adapter->getNbpol());
        $pcg->setIprpcg($adapter->getIprpcg());
        $pcg->setMutpcg($adapter->getMutpcg());
        $pcg->setDamp($adapter->getDamp());
        $pcg->setDampt($adapter->getDampt());
        $pcg->setIhcofadd($adapter->getIhcofadd());
        $pcg->setExtension($adapter->getExtension());
        $pcg->setUnitnumber($adapter->getUnitnumber());

        return $pcg;
    }
}
