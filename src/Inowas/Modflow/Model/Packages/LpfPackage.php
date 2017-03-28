<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

use Inowas\Common\Modflow\Chani;
use Inowas\Common\Modflow\ConstantCv;
use Inowas\Common\Modflow\Extension;
use Inowas\Common\Modflow\Hani;
use Inowas\Common\Modflow\Hdry;
use Inowas\Common\Modflow\Hk;
use Inowas\Common\Modflow\IhdWet;
use Inowas\Common\Modflow\Ipakcb;
use Inowas\Common\Modflow\IWetIt;
use Inowas\Common\Modflow\LayAvg;
use Inowas\Common\Modflow\LayTyp;
use Inowas\Common\Modflow\LayVka;
use Inowas\Common\Modflow\LayWet;
use Inowas\Common\Modflow\NoCvCorrection;
use Inowas\Common\Modflow\NoVfc;
use Inowas\Common\Modflow\Ss;
use Inowas\Common\Modflow\StorageCoefficient;
use Inowas\Common\Modflow\Sy;
use Inowas\Common\Modflow\ThickStrt;
use Inowas\Common\Modflow\UnitNumber;
use Inowas\Common\Modflow\Vka;
use Inowas\Common\Modflow\Vkcb;
use Inowas\Common\Modflow\WetDry;
use Inowas\Common\Modflow\WetFct;

class LpfPackage implements \JsonSerializable
{

    /** @var  LayTyp */
    protected $laytyp;

    /** @var  LayAvg */
    protected $layavg;

    /** @var  Chani */
    protected $chani;

    /** @var  LayVka */
    protected $layvka;

    /** @var  LayWet */
    protected $laywet;

    /** @var  Ipakcb */
    protected $ipakcb;

    /** @var  Hdry */
    protected $hdry;

    /** @var  WetFct */
    protected $wetfct;

    /** @var  IWetIt */
    protected $iwetit;

    /** @var  IhdWet */
    protected $ihdwet;

    /** @var  Hk */
    protected $hk;

    /** @var  Hani */
    protected $hani;

    /** @var Vka */
    protected $vka;

    /** @var  Ss */
    protected $ss;

    /** @var  Sy */
    protected $sy;

    /** @var  Vkcb */
    protected $vkcb;

    /** @var  WetDry */
    protected $wetdry;

    /** @var  StorageCoefficient */
    protected $storagecoefficient;

    /** @var  ConstantCv */
    protected $constantcv;

    /** @var  ThickStrt */
    protected $thickstrt;

    /** @var  NoCvCorrection */
    protected $nocvcorrection;

    /** @var  NoVfc */
    protected $novfc;

    /** @var  Extension */
    protected $extension;

    /** @var  UnitNumber */
    protected $unitnumber;


    public static function fromParams(
        ?LayTyp $layTyp = null,
        ?LayAvg $layAvg = null,
        ?Chani $chani = null,
        ?LayVka $layVka = null,
        ?LayWet $layWet = null,
        ?Ipakcb $ipakcb = null,
        ?Hdry $hdry = null,
        ?WetFct $wetFct = null,
        ?IWetIt $iWetIt = null,
        ?IhdWet $ihdWet = null,
        ?Hk $hk = null,
        ?Hani $hani = null,
        ?Vka $vka = null,
        ?Ss $ss = null,
        ?Sy $sy = null,
        ?Vkcb $vkcb = null,
        ?WetDry $wetDry = null,
        ?StorageCoefficient $storageCoefficient = null,
        ?ConstantCv $constantCv = null,
        ?ThickStrt $thickStrt = null,
        ?NoCvCorrection $noCvCorrection = null,
        ?NoVfc $noVfc = null,
        ?Extension $extension = null,
        ?UnitNumber $unitNumber = null
    ): LpfPackage
    {
        $self = new self();
        $self->laytyp = $layTyp;
        $self->layavg = $layAvg;
        $self->chani = $chani;
        $self->layvka = $layVka;
        $self->laywet = $layWet;
        $self->ipakcb = $ipakcb;
        $self->hdry = $hdry;
        $self->wetfct = $wetFct;
        $self->iwetit = $iWetIt;
        $self->ihdwet = $ihdWet;
        $self->hk = $hk;
        $self->hani = $hani;
        $self->vka = $vka;
        $self->ss = $ss;
        $self->sy = $sy;
        $self->vkcb = $vkcb;
        $self->wetdry = $wetDry;
        $self->storagecoefficient = $storageCoefficient;
        $self->constantcv = $constantCv;
        $self->thickstrt = $thickStrt;
        $self->nocvcorrection = $noCvCorrection;
        $self->novfc = $noVfc;
        $self->extension = $extension;
        $self->unitnumber = $unitNumber;

        if (! $self->laytyp instanceof LayTyp){
            $self->laytyp = LayTyp::fromInt(0);
        }

        if (! $self->layavg instanceof LayAvg) {
            $self->layavg = LayAvg::fromInt(0);
        }

        if (! $self->chani instanceof Chani) {
            $self->chani = Chani::fromFloat(1.0);
        }

        if (! $self->layvka instanceof LayVka) {
            $self->layvka = LayVka::fromFloat(0);
        }

        if (! $self->laywet instanceof LayWet) {
            $self->laywet = LayWet::fromFloat(0);
        }

        if (! $self->ipakcb instanceof Ipakcb) {
            $self->ipakcb = Ipakcb::fromInteger(53);
        }

        if (! $self->hdry instanceof Hdry) {
            $self->hdry = Hdry::fromFloat(-1E30);
        }

        if (! $self->wetfct instanceof WetFct) {
            $self->wetfct = WetFct::fromFloat(0.1);
        }

        if (! $self->iwetit instanceof IWetIt) {
            $self->iwetit = IWetIt::fromInteger(1);
        }

        if (! $self->ihdwet instanceof IhdWet) {
            $self->ihdwet = IhdWet::fromInteger(0);
        }

        if (! $self->hk instanceof Hk) {
            $self->hk = Hk::fromValue(1.0);
        }

        if (! $self->hani instanceof Hani) {
            $self->hani = Hani::fromValue(1.0);
        }

        if (! $self->vka instanceof Vka) {
            $self->vka = Vka::fromFloat(1.0);
        }

        if (! $self->ss instanceof Ss) {
            $self->ss = Ss::fromFloat(1e-5);
        }

        if (! $self->sy instanceof Sy) {
            $self->sy = Sy::fromFloat(0.15);
        }

        if (! $self->vkcb instanceof Vkcb) {
            $self->vkcb = Vkcb::fromFloat(0.0);
        }

        if (! $self->wetdry instanceof WetDry) {
            $self->wetdry = WetDry::fromFloat(-0.01);
        }

        if (! $self->storagecoefficient instanceof StorageCoefficient) {
            $self->storagecoefficient = StorageCoefficient::fromBool(false);
        }

        if (! $self->constantcv instanceof ConstantCv) {
            $self->constantcv = ConstantCv::fromBool(false);
        }

        if (! $self->thickstrt instanceof ThickStrt) {
            $self->thickstrt = ThickStrt::fromBool(false);
        }

        if (! $self->nocvcorrection instanceof NoCvCorrection) {
            $self->nocvcorrection = NoCvCorrection::fromBool(false);
        }

        if (! $self->novfc instanceof NoVfc) {
            $self->novfc = NoVfc::fromBool(false);
        }

        if (! $self->extension instanceof Extension) {
            $self->extension = Extension::fromString('bas');
        }

        if (! $self->unitnumber instanceof UnitNumber) {
            $self->unitnumber = UnitNumber::fromInteger(13);
        }

        return $self;
    }

    public function toArray(): array
    {
        return array(
            "laytyp" => $this->laytyp->toValue(),
            "layavg" => $this->layavg->toValue(),
            "chani" => $this->chani->toValue(),
            "layvka" => $this->layvka->toValue(),
            "laywet" => $this->laywet->toValue(),
            "ipakcb" => $this->ipakcb->toValue(),
            "hdry" => $this->hdry->toValue(),
            "wetfct" => $this->wetfct->toValue(),
            "iwetit" => $this->iwetit->toValue(),
            "ihdwet" => $this->ihdwet->toValue(),
            "hk" => $this->hk->toValue(),
            "hani" => $this->hani->toValue(),
            "vka" => $this->vka->toValue(),
            "ss" => $this->ss->toValue(),
            "sy" => $this->sy->toValue(),
            "vkcb" => $this->vkcb->toValue(),
            "wetdry" => $this->wetdry->toValue(),
            "storagecoefficient" => $this->storagecoefficient->toValue(),
            "constantcv" => $this->constantcv->toValue(),
            "thickstrt" => $this->thickstrt->toValue(),
            "nocvcorrection" => $this->nocvcorrection->toValue(),
            "novfc" => $this->novfc->toValue(),
            "extension" => $this->extension->toValue(),
            "unitnumber" => $this->unitnumber->toValue()
        );
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }
}
