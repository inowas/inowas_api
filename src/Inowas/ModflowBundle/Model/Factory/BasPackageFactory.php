<?php

namespace Inowas\ModflowBundle\Model\Factory;

use Inowas\ModflowBundle\Model\Adapter\BasPackageAdapter;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\Package\BasPackage;
use Inowas\Soilmodel\Model\Soilmodel;

class BasPackageFactory implements PackageFactoryInterface
{
    public function create(ModflowModel $model, Soilmodel $soilmodel){
        $bas = new BasPackage();
        $adapter = new BasPackageAdapter($model, $soilmodel);

        $bas->setIbound($adapter->getIbound());
        $bas->setStrt($adapter->getStrt());
        $bas->setIfrefm($adapter->isIfrefm());
        $bas->setIxsec($adapter->isIxsec());
        $bas->setIchflg($adapter->isIchflg());
        $bas->setStoper($adapter->getStoper());
        $bas->setHnoflo($adapter->getHnoflo());
        $bas->setExtension($adapter->getExtension());
        $bas->setUnitnumber($adapter->getUnitnumber());

        return $bas;
    }
}
