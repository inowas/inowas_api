<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;

class DisPackageFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){

        $dis = new DisPackage();
        $adapter = new DisPackageAdapter($model);

        $dis->setNlay($adapter->getNlay());
        $dis->setNrow($adapter->getNRow());
        $dis->setNcol($adapter->getNCol());
        $dis->setNper($adapter->getNper());
        $dis->setDelr($adapter->getDelr());
        $dis->setDelc($adapter->getDelc());
        $dis->setLaycbd($adapter->getLaycbd());
        $dis->setTop($adapter->getTop());
        $dis->setBotm($adapter->getBotm());
        $dis->setPerlen($adapter->getPerlen());
        $dis->setNstp($adapter->getNstp());
        $dis->setTsmult($adapter->getTsmult());
        $dis->setSteady($adapter->getSteady());
        $dis->setItmuni($adapter->getItmuni());
        $dis->setLenuni($adapter->getLenuni());
        $dis->setExtension($adapter->getExtension());
        $dis->setUnitnumber($adapter->getUnitnumber());
        $dis->setXul($adapter->getXul());
        $dis->setYul($adapter->getYul());
        $dis->setRotation($adapter->getRotation());
        $dis->setProj4Str($adapter->getProj4Str());
        $dis->setStartDatetime($adapter->getStartDateTime());

        return $dis;
    }
}
