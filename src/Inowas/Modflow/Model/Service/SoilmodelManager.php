<?php

namespace Inowas\Modflow\Model\Service;

use Inowas\Common\Grid\Nlay;
use Inowas\Common\Modflow\Botm;
use Inowas\Common\Modflow\Chani;
use Inowas\Common\Modflow\Constantcv;
use Inowas\Common\Modflow\Hani;
use Inowas\Common\Modflow\Hdry;
use Inowas\Common\Modflow\Hk;
use Inowas\Common\Modflow\Ihdwet;
use Inowas\Common\Modflow\Ipakcb;
use Inowas\Common\Modflow\Iwetit;
use Inowas\Common\Modflow\Layavg;
use Inowas\Common\Modflow\Laytyp;
use Inowas\Common\Modflow\Layvka;
use Inowas\Common\Modflow\Laywet;
use Inowas\Common\Modflow\Nocvcorrection;
use Inowas\Common\Modflow\Novfc;
use Inowas\Common\Modflow\Ss;
use Inowas\Common\Modflow\Storagecoefficient;
use Inowas\Common\Modflow\Sy;
use Inowas\Common\Modflow\Thickstrt;
use Inowas\Common\Modflow\Top;
use Inowas\Common\Modflow\Vka;
use Inowas\Common\Modflow\Vkcb;
use Inowas\Common\Modflow\Wetdry;
use Inowas\Common\Modflow\Wetfct;
use Inowas\Soilmodel\Model\SoilmodelId;
use Inowas\Soilmodel\Projection\LayerDetails\LayerValuesFinder;

class SoilmodelManager implements SoilmodelManagerInterface
{
    /** @var  LayerValuesFinder */
    protected $layerValuesFinder;

    public function __construct(LayerValuesFinder $layerValuesFinder){
        $this->layerValuesFinder = $layerValuesFinder;
    }

    public function getNlay(SoilmodelId $soilmodelId): Nlay
    {
        return $this->layerValuesFinder->getNlay($soilmodelId);
    }

    public function getTop(SoilmodelId $soilmodelId): Top
    {
        return $this->layerValuesFinder->getTop($soilmodelId);
    }

    public function getBotm(SoilmodelId $soilmodelId): Botm
    {
        return $this->layerValuesFinder->getBotm($soilmodelId);
    }

    public function getLaytyp(SoilmodelId $soilmodelId): Laytyp
    {
        return $this->layerValuesFinder->getLaytyp($soilmodelId);
    }

    public function getLayavg(SoilmodelId $soilmodelId): Layavg
    {
        return $this->layerValuesFinder->getLayavg($soilmodelId);
    }

    public function getChani(SoilmodelId $soilmodelId): Chani
    {
        return $this->layerValuesFinder->getChani($soilmodelId);
    }

    public function getLayvka(SoilmodelId $soilmodelId): Layvka
    {
        return $this->layerValuesFinder->getLayvka($soilmodelId);
    }

    public function getLaywet(SoilmodelId $soilmodelId): Laywet
    {
        return $this->layerValuesFinder->getLaywet($soilmodelId);
    }

    public function getIpakcb(SoilmodelId $soilmodelId): Ipakcb
    {
        return Ipakcb::fromValue(53);
    }

    public function getHdry(SoilmodelId $soilmodelId): Hdry
    {
        return Hdry::fromValue(-1e30);
    }

    public function getWetfct(SoilmodelId $soilmodelId): Wetfct
    {
        return Wetfct::fromValue(0.1);
    }

    public function getIwetit(SoilmodelId $soilmodelId): Iwetit
    {
        return Iwetit::fromValue(1);
    }

    public function getIhdwet(SoilmodelId $soilmodelId): Ihdwet
    {
        return Ihdwet::fromValue(0);
    }

    public function getHk(SoilmodelId $soilmodelId): Hk
    {
        return $this->layerValuesFinder->getHk($soilmodelId);
    }

    public function getHani(SoilmodelId $soilmodelId): Hani
    {
        return $this->layerValuesFinder->getHani($soilmodelId);
    }


    public function getVka(SoilmodelId $soilmodelId): Vka
    {
        return $this->layerValuesFinder->getVka($soilmodelId);
    }

    public function getSs(SoilmodelId $soilmodelId): Ss
    {
        return $this->layerValuesFinder->getSs($soilmodelId);
    }


    public function getSy(SoilmodelId $soilmodelId): Sy
    {
        return $this->layerValuesFinder->getSy($soilmodelId);
    }

    public function getVkcb(SoilmodelId $soilmodelId): Vkcb
    {
        return Vkcb::fromValue(0.0);
    }

    public function getWetdry(SoilmodelId $soilmodelId): Wetdry
    {
        return Wetdry::fromValue(0.1);
    }

    public function getStoragecoefficient(SoilmodelId $soilmodelId): Storagecoefficient
    {
        return Storagecoefficient::fromBool(false);
    }

    public function getConstantcv(SoilmodelId $soilmodelId): Constantcv
    {
        return Constantcv::fromBool(false);
    }

    public function getThickstrt(SoilmodelId $soilmodelId): Thickstrt
    {
        return Thickstrt::fromBool(false);
    }

    public function getNocvcorrection(SoilmodelId $soilmodelId): Nocvcorrection
    {
        return Nocvcorrection::fromBool(false);
    }

    public function getNovfc(SoilmodelId $soilmodelId): Novfc
    {
        return Novfc::fromBool(false);
    }
}
