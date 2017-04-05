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

interface SoilmodelManagerInterface
{
    public function getNlay(SoilmodelId $soilmodelId): Nlay;

    public function getTop(SoilmodelId $soilmodelId): Top;

    public function getBotm(SoilmodelId $soilmodelId): Botm;

    public function getLaytyp(SoilmodelId $soilmodelId): Laytyp;

    public function getLayavg(SoilmodelId $soilmodelId): Layavg;

    public function getChani(SoilmodelId $soilmodelId): Chani;

    public function getLayvka(SoilmodelId $soilmodelId): Layvka;

    public function getLaywet(SoilmodelId $soilmodelId): Laywet;

    public function getIpakcb(SoilmodelId $soilmodelId): Ipakcb;

    public function getHdry(SoilmodelId $soilmodelId): Hdry;

    public function getWetfct(SoilmodelId $soilmodelId): Wetfct;

    public function getIwetit(SoilmodelId $soilmodelId): Iwetit;

    public function getIhdwet(SoilmodelId $soilmodelId): Ihdwet;

    public function getHk(SoilmodelId $soilmodelId): Hk;

    public function getHani(SoilmodelId $soilmodelId): Hani;

    public function getVka(SoilmodelId $soilmodelId): Vka;

    public function getSs(SoilmodelId $soilmodelId): Ss;

    public function getSy(SoilmodelId $soilmodelId): Sy;

    public function getVkcb(SoilmodelId $soilmodelId): Vkcb;

    public function getWetdry(SoilmodelId $soilmodelId): Wetdry;

    public function getStoragecoefficient(SoilmodelId $soilmodelId): Storagecoefficient;

    public function getConstantcv(SoilmodelId $soilmodelId): Constantcv;

    public function getThickstrt(SoilmodelId $soilmodelId): Thickstrt;

    public function getNocvcorrection(SoilmodelId $soilmodelId): Nocvcorrection;

    public function getNovfc(SoilmodelId $soilmodelId): Novfc;

}
