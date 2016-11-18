<?php

namespace Inowas\FlopyBundle\Model\Factory;

use Inowas\ModflowBundle\Model\Adapter\LpfPackageAdapter;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\Package\LpfPackage;
use Inowas\SoilmodelBundle\Model\Soilmodel;

class LpfPackageFactory implements PackageFactoryInterface
{
    public function create(ModflowModel $model, Soilmodel $soilmodel){

        $lpf = new LpfPackage();
        $adapter = new LpfPackageAdapter($model, $soilmodel);

        $lpf->setLaytyp($adapter->getLaytyp());
        $lpf->setLayavg($adapter->getLayavg());
        $lpf->setChani($adapter->getChani());
        $lpf->setLayvka($adapter->getLayvka());
        $lpf->setLaywet($adapter->getLaywet());
        $lpf->setIpakcb($adapter->getIpakcb());
        $lpf->setHdry($adapter->getHdry());
        $lpf->setIwdflg($adapter->getIwdflg());
        $lpf->setWetfct($adapter->getWetfct());
        $lpf->setIwetit($adapter->getIwetit());
        $lpf->setIhdwet($adapter->getIhdwet());
        $lpf->setHk($adapter->getHk());
        $lpf->setHani($adapter->getHani());
        $lpf->setVka($adapter->getVka());
        $lpf->setSs($adapter->getSs());
        $lpf->setSy($adapter->getSy());
        $lpf->setVkcb($adapter->getVkcb());
        $lpf->setWetdry($adapter->getWetdry());
        $lpf->setStoragecoefficient($adapter->isStoragecoefficient());
        $lpf->setConstantcv($adapter->isConstantcv());
        $lpf->setThickstrt($adapter->isThickstrt());
        $lpf->setNocvcorrection($adapter->isNocvcorrection());
        $lpf->setNovfc($adapter->isNovfc());
        $lpf->setExtension($adapter->getExtension());
        $lpf->setUnitnumber($adapter->getUnitnumber());

        return $lpf;
    }
}
