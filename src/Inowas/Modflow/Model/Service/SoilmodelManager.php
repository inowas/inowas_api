<?php

namespace Inowas\Modflow\Model\Service;

use Inowas\Common\Grid\Nlay;
use Inowas\Common\Modflow\Botm;
use Inowas\Common\Modflow\Chani;
use Inowas\Common\Modflow\Constantcv;
use Inowas\Common\Modflow\Hani;
use Inowas\Common\Modflow\Hk;
use Inowas\Common\Modflow\Ihdwet;
use Inowas\Common\Modflow\Iwetit;
use Inowas\Common\Modflow\Layavg;
use Inowas\Common\Modflow\Laytyp;
use Inowas\Common\Modflow\Layvka;
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
use Inowas\Soilmodel\Projection\LayerDetails\LayerDetailsFinder;
use Inowas\Soilmodel\Projection\LayerDetails\LayerValuesFinder;

class SoilmodelManager implements SoilmodelManagerInterface
{

    /** @var  LayerDetailsFinder */
    protected $layerDetailsFinder;

    /** @var  LayerValuesFinder */
    protected $layerValuesFinder;

    public function __construct(LayerValuesFinder $layerValuesFinder, LayerDetailsFinder $layerDetailsFinder){
        $this->layerDetailsFinder = $layerDetailsFinder;
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
        return $this->layerDetailsFinder->getLaytyp($soilmodelId);
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Layavg
     */
    public function getLayavg(SoilmodelId $soilmodelId): Layavg
    {
        // TODO: Implement getLayavg() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Chani
     */
    public function getChani(SoilmodelId $soilmodelId): Chani
    {
        // TODO: Implement getChani() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Layvka
     */
    public function getLayvka(SoilmodelId $soilmodelId): Layvka
    {
        // TODO: Implement getLayvka() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Wetfct
     */
    public function getWetfct(SoilmodelId $soilmodelId): Wetfct
    {
        // TODO: Implement getWetfct() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Iwetit
     */
    public function getIwetit(SoilmodelId $soilmodelId): Iwetit
    {
        // TODO: Implement getIwetit() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Ihdwet
     */
    public function getIhdwet(SoilmodelId $soilmodelId): Ihdwet
    {
        // TODO: Implement getIhdwet() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Hk
     */
    public function getHk(SoilmodelId $soilmodelId): Hk
    {
        // TODO: Implement getHk() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Hani
     */
    public function getHani(SoilmodelId $soilmodelId): Hani
    {
        // TODO: Implement getHani() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Vka
     */
    public function getVka(SoilmodelId $soilmodelId): Vka
    {
        // TODO: Implement getVka() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Ss
     */
    public function getSs(SoilmodelId $soilmodelId): Ss
    {
        // TODO: Implement getSs() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Sy
     */
    public function getSy(SoilmodelId $soilmodelId): Sy
    {
        // TODO: Implement getSy() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Vkcb
     */
    public function getVkcb(SoilmodelId $soilmodelId): Vkcb
    {
        // TODO: Implement getVkcb() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Wetdry
     */
    public function getWetdry(SoilmodelId $soilmodelId): Wetdry
    {
        // TODO: Implement getWetdry() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Storagecoefficient
     */
    public function getStoragecoefficient(SoilmodelId $soilmodelId): Storagecoefficient
    {
        // TODO: Implement getStoragecoefficient() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Constantcv
     */
    public function getConstantcv(SoilmodelId $soilmodelId): Constantcv
    {
        // TODO: Implement getConstantcv() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Thickstrt
     */
    public function getThickstrt(SoilmodelId $soilmodelId): Thickstrt
    {
        // TODO: Implement getThickstrt() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Nocvcorrection
     */
    public function getNocvcorrection(SoilmodelId $soilmodelId): Nocvcorrection
    {
        // TODO: Implement getNocvcorrection() method.
    }

    /**
     * @param SoilmodelId $soilmodelId
     * @return Novfc
     */
    public function getNovfc(SoilmodelId $soilmodelId): Novfc
    {
        // TODO: Implement getNovfc() method.
    }


}
