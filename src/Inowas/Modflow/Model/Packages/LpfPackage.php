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

class LpfPackage implements PackageInterface
{
    /** @var string  */
    protected $type = 'lpf';

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

    /**
     * @return LpfPackage
     */
    public static function fromDefaults(): LpfPackage
    {
        $laytyp = LayTyp::fromInt(0);
        $layavg = LayAvg::fromInt(0);
        $chani = Chani::fromFloat(1.0);
        $layvka = LayVka::fromFloat(0);
        $laywet = LayWet::fromFloat(0);
        $ipakcb = Ipakcb::fromInteger(53);
        $hdry = Hdry::fromFloat(-1E30);
        $wetfct = WetFct::fromFloat(0.1);
        $iwetit = IWetIt::fromInteger(1);
        $ihdwet = IhdWet::fromInteger(0);
        $hk = Hk::fromValue(1.0);
        $hani = Hani::fromValue(1.0);
        $vka = Vka::fromFloat(1.0);
        $ss = Ss::fromFloat(1e-5);
        $sy = Sy::fromFloat(0.15);
        $vkcb = Vkcb::fromFloat(0.0);
        $wetdry = WetDry::fromFloat(-0.01);
        $storagecoefficient = StorageCoefficient::fromBool(false);
        $constantcv = ConstantCv::fromBool(false);
        $thickstrt = ThickStrt::fromBool(false);
        $nocvcorrection = NoCvCorrection::fromBool(false);
        $novfc = NoVfc::fromBool(false);
        $extension = Extension::fromString('bas');
        $unitnumber = UnitNumber::fromInteger(13);

        return new self(
            $laytyp, $layavg, $chani, $layvka,
            $laywet, $ipakcb, $hdry, $wetfct,
            $iwetit, $ihdwet, $hk, $hani, $vka,
            $ss, $sy, $vkcb, $wetdry, $storagecoefficient,
            $constantcv, $thickstrt, $nocvcorrection,
            $novfc, $extension, $unitnumber
        );
    }

    public static function fromParams(
        LayTyp $laytyp,
        LayAvg $layavg,
        Chani $chani,
        LayVka $layvka,
        LayWet $laywet,
        Ipakcb $ipakcb,
        Hdry $hdry,
        WetFct $wetfct,
        IWetIt $iwetit,
        IhdWet $ihdwet,
        Hk $hk,
        Hani $hani,
        Vka $vka,
        Ss $ss,
        Sy $sy,
        Vkcb $vkcb,
        WetDry $wetdry,
        StorageCoefficient $storagecoefficient,
        ConstantCv $constantcv,
        ThickStrt $thickstrt,
        NoCvCorrection $nocvcorrection,
        NoVfc $novfc,
        Extension $extension,
        UnitNumber $unitnumber
    ): LpfPackage
    {
        return new self(
            $laytyp, $layavg, $chani, $layvka,
            $laywet, $ipakcb, $hdry, $wetfct,
            $iwetit, $ihdwet, $hk, $hani, $vka,
            $ss, $sy, $vkcb, $wetdry, $storagecoefficient,
            $constantcv, $thickstrt, $nocvcorrection,
            $novfc, $extension, $unitnumber
        );
    }

    public static function fromArray(array $arr): LpfPackage
    {
        $laytyp = LayTyp::fromValue($arr["laytyp"]);
        $layavg = LayAvg::fromValue($arr["layavg"]);
        $chani = Chani::fromValue($arr["chani"]);
        $layvka = LayVka::fromValue($arr["layvka"]);
        $laywet = LayWet::fromValue($arr["laywet"]);
        $ipakcb = Ipakcb::fromValue($arr["ipakcb"]);
        $hdry = Hdry::fromValue($arr["hdry"]);
        $wetfct = WetFct::fromValue($arr["wetfct"]);
        $iwetit = IWetIt::fromValue($arr["iwetit"]);
        $ihdwet = IhdWet::fromValue($arr["ihdwet"]);
        $hk = Hk::fromValue($arr["hk"]);
        $hani = Hani::fromValue($arr["hani"]);
        $vka = Vka::fromValue($arr["vka"]);
        $ss = Ss::fromValue($arr["ss"]);
        $sy = Sy::fromValue($arr["sy"]);
        $vkcb = Vkcb::fromValue($arr["vkcb"]);
        $wetdry = WetDry::fromValue($arr["wetdry"]);
        $storagecoefficient = StorageCoefficient::fromValue($arr["storagecoefficient"]);
        $constantcv = ConstantCv::fromValue($arr["constantcv"]);
        $thickstrt = ThickStrt::fromValue($arr["thickstrt"]);
        $nocvcorrection = NoCvCorrection::fromValue($arr["nocvcorrection"]);
        $novfc = NoVfc::fromValue($arr["novfc"]);
        $extension = Extension::fromValue($arr["extension"]);
        $unitnumber = UnitNumber::fromValue($arr["unitnumber"]);

        return new self(
            $laytyp, $layavg, $chani, $layvka,
            $laywet, $ipakcb, $hdry, $wetfct,
            $iwetit, $ihdwet, $hk, $hani, $vka,
            $ss, $sy, $vkcb, $wetdry, $storagecoefficient,
            $constantcv, $thickstrt, $nocvcorrection,
            $novfc, $extension, $unitnumber
        );
    }

    public function type(): string
    {
        return $this->type;
    }

    private function __construct(
        LayTyp $layTyp,
        LayAvg $layAvg,
        Chani $chani,
        LayVka $layVka,
        LayWet $layWet,
        Ipakcb $ipakcb,
        Hdry $hdry,
        WetFct $wetFct,
        IWetIt $iWetIt,
        IhdWet $ihdWet,
        Hk $hk,
        Hani $hani,
        Vka $vka,
        Ss $ss,
        Sy $sy,
        Vkcb $vkcb,
        WetDry $wetDry,
        StorageCoefficient $storageCoefficient,
        ConstantCv $constantCv,
        ThickStrt $thickStrt,
        NoCvCorrection $noCvCorrection,
        NoVfc $noVfc,
        Extension $extension,
        UnitNumber $unitNumber
    )
    {
        $this->laytyp = $layTyp;
        $this->layavg = $layAvg;
        $this->chani = $chani;
        $this->layvka = $layVka;
        $this->laywet = $layWet;
        $this->ipakcb = $ipakcb;
        $this->hdry = $hdry;
        $this->wetfct = $wetFct;
        $this->iwetit = $iWetIt;
        $this->ihdwet = $ihdWet;
        $this->hk = $hk;
        $this->hani = $hani;
        $this->vka = $vka;
        $this->ss = $ss;
        $this->sy = $sy;
        $this->vkcb = $vkcb;
        $this->wetdry = $wetDry;
        $this->storagecoefficient = $storageCoefficient;
        $this->constantcv = $constantCv;
        $this->thickstrt = $thickStrt;
        $this->nocvcorrection = $noCvCorrection;
        $this->novfc = $noVfc;
        $this->extension = $extension;
        $this->unitnumber = $unitNumber;
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
