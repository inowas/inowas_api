<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;

class BasPackageFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){
        $bas = new BasPackage();
        $adapter = new BasPackageAdapter($model);

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
