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

class BasPackage implements \JsonSerializable
{

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

    public static function fromParams(
        ?IBound $iBound = null,
        ?Strt $strt = null,
        ?Ixsec $ixsec = null,
        ?IchFlg $ichFlg = null,
        ?StoPer $stoPer = null,
        ?HNoFlo $hnoFlo = null,
        ?Extension $extension = null,
        ?UnitNumber $unitNumber = null
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

        if (! $self->iBound instanceof IBound){
            $self->iBound = IBound::fromValue(1);
        }

        if (! $self->strt instanceof Strt) {
            $self->strt = Strt::fromValue(1.0);
        }

        if (! $self->ixsec instanceof Ixsec) {
            $self->ixsec = Ixsec::fromBool(false);
        }

        if (! $self->ichflg instanceof IchFlg) {
            $self->ichflg = IchFlg::fromBool(false);
        }

        if (! $self->stoper instanceof StoPer) {
            $self->stoper = StoPer::none();
        }

        if (! $self->hnoflo instanceof HNoFlo) {
            $self->hnoflo = HNoFlo::fromFloat(-999.99);
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
            "ibound" => $this->iBound->toValue(),
            "strt" => $this->strt->toValue(),
            "ixsec" => $this->ixsec->toBool(),
            "ichflg" => $this->ichflg->toBool(),
            "stoper" => $this->stoper->value(),
            "hnoflo" => $this->hnoflo->toFloat(),
            "extension" => $this->extension->toString(),
            "unitnumber" => $this->unitnumber->toInteger()
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
