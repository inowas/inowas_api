<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

use Inowas\Common\Modflow\Extension;
use Inowas\Common\Modflow\HNoFlo;
use Inowas\Common\Modflow\IBound;
use Inowas\Common\Modflow\IchFlg;
use Inowas\Common\Modflow\Ixsec;
use Inowas\Common\Modflow\StoPer;
use Inowas\Common\Modflow\Strt;
use Inowas\Common\Modflow\UnitNumber;

class BasPackage implements PackageInterface
{

    /** @var string  */
    protected $type = 'bas';

    /** @var  IBound */
    protected $iBound;

    /** @var  Strt */
    protected $strt;

    /** @var Ixsec */
    protected $ixsec;

    /** @var IchFlg */
    protected $ichflg;

    /** @var StoPer */
    protected $stoper;

    /** @var HNoFlo */
    protected $hnoflo;

    /** @var Extension */
    protected $extension;

    /** @var UnitNumber  */
    protected $unitnumber;

    public static function fromDefaults(): BasPackage
    {
        $iBound =
        $strt = Strt::fromValue(1.0);
        $ixsec = Ixsec::fromBool(false);
        $ichflg = IchFlg::fromBool(false);
        $stoper = StoPer::none();
        $hnoflo = HNoFlo::fromFloat(-999.99);
        $extension = Extension::fromString('bas');
        $unitnumber = UnitNumber::fromInteger(13);

        $self = new self();
        $self->iBound = $iBound;
        $self->strt = $strt;
        $self->ixsec = $ixsec;
        $self->ichflg = $ichflg;
        $self->stoper = $stoper;
        $self->hnoflo = $hnoflo;
        $self->extension = $extension;
        $self->unitnumber = $unitnumber;
        return $self;
    }

    public static function fromParams(
        IBound $iBound,
        Strt $strt,
        Ixsec $ixsec,
        IchFlg $ichFlg,
        StoPer $stoPer,
        HNoFlo $hnoFlo,
        Extension $extension,
        UnitNumber $unitNumber
    ): BasPackage
    {
        $self = new self();
        $self->iBound = $iBound;
        $self->strt = $strt;
        $self->ixsec = $ixsec;
        $self->ichflg = $ichFlg;
        $self->stoper = $stoPer;
        $self->hnoflo = $hnoFlo;
        $self->extension = $extension;
        $self->unitnumber = $unitNumber;

        return $self;
    }

    public static function fromArray(array $arr): BasPackage
    {
        $self = new self();
        $self->iBound = IBound::fromValue($arr['ibound']);
        $self->strt = Strt::fromValue($arr['strt']);
        $self->ixsec = Ixsec::fromValue($arr['ixsec']);
        $self->ichflg = IchFlg::fromValue($arr['ichflg']);
        $self->stoper = StoPer::fromValue($arr['stoper']);
        $self->hnoflo = HNoFlo::fromFloat($arr['hnoflo']);
        $self->extension = Extension::fromValue($arr['extension']);
        $self->unitnumber = UnitNumber::fromValue($arr['unitnumber']);

        return $self;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function iBound(): IBound
    {
        return $this->iBound;
    }

    public function strt(): Strt
    {
        return $this->strt;
    }

    public function ixsec(): Ixsec
    {
        return $this->ixsec;
    }

    public function ichflg(): IchFlg
    {
        return $this->ichflg;
    }

    public function stoper(): StoPer
    {
        return $this->stoper;
    }

    public function hnoflo(): HNoFlo
    {
        return $this->hnoflo;
    }

    public function extension(): Extension
    {
        return $this->extension;
    }

    public function unitnumber(): UnitNumber
    {
        return $this->unitnumber;
    }

    public function toArray(): array
    {
        return array(
            "ibound" => $this->iBound->toValue(),
            "strt" => $this->strt->toValue(),
            "ixsec" => $this->ixsec->toValue(),
            "ichflg" => $this->ichflg->toValue(),
            "stoper" => $this->stoper->toValue(),
            "hnoflo" => $this->hnoflo->toValue(),
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
