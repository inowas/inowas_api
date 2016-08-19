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
        $dis->setStartDatetime($adapter->getStartDateTime());

        return $dis;
    }
}