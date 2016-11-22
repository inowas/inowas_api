<?php

namespace Inowas\Flopy\Model\Factory;

use Inowas\Flopy\Model\Adapter\BasPackageAdapter;
use Inowas\Flopy\Model\Package\BasPackage;
use Inowas\Flopy\Model\Package\PackageInterface;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\SoilmodelBundle\Model\Soilmodel;

class BasPackageFactory implements PackageFactoryInterface
{
    public function create(ModflowModel $model, Soilmodel $soilmodel): PackageInterface {
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
