<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Packages;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Grid\Distance;
use Inowas\Common\Grid\Nlay;
use Inowas\Common\Modflow\Botm;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\Ncol;
use Inowas\Common\Grid\Delc;
use Inowas\Common\Grid\Delr;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Grid\Laycbd;
use Inowas\Common\Grid\Proj4str;
use Inowas\Common\Grid\Rotation;
use Inowas\Common\Grid\Nrow;
use Inowas\Common\Modflow\Top;
use Inowas\Common\Modflow\Extension;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Nstp;
use Inowas\Common\Modflow\Steady;
use Inowas\Common\Modflow\Perlen;
use Inowas\Common\Modflow\Nper;
use Inowas\Common\Modflow\Tsmult;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Modflow\Unitnumber;
use Inowas\Common\Modflow\Xul;
use Inowas\Common\Modflow\Yul;

/**
 * Class DisPackage
 * @package Inowas\ModflowModel\Model\Packages
 */
class DisPackage extends AbstractPackage
{
    public const TYPE = 'dis';
    public const DESCRIPTION = 'Discretization Package';

    /** @var  Nlay */
    protected $nlay;

    /** @var  Nrow */
    protected $nrow;

    /** @var  Ncol */
    protected $ncol;

    /** @var  Nper */
    protected $nper;

    /** @var  Delr */
    protected $delr;

    /** @var  Delc */
    protected $delc;

    /** @var  Laycbd */
    protected $laycbd;

    /** @var  Top */
    protected $top;

    /** @var  Botm */
    protected $botm;

    /** @var  Perlen */
    protected $perlen;

    /** @var  Nstp */
    protected $nstp;

    /** @var  Tsmult */
    protected $tsmult;

    /** @var  Steady */
    protected $steady;

    /** @var  TimeUnit */
    protected $itmuni;

    /** @var  LengthUnit */
    protected $lenuni;

    /** @var  Extension */
    protected $extension;

    /** @var Unitnumber */
    protected $unitnumber;

    /** @var  Xul */
    protected $xul;

    /** @var  Yul */
    protected $yul;

    /** @var  Rotation */
    protected $rotation;

    /** @var  Proj4str */
    protected $proj4str;

    /** @var  DateTime */
    protected $startDateTime;

    /**
     * @return DisPackage
     */
    public static function fromDefaults(): DisPackage
    {
        // DEFAULT
        $nlay = Nlay::fromInt(1);
        $ncol = Ncol::fromInt(1);
        $nrow = Nrow::fromInt(1);
        $nper = Nper::fromInteger(1);
        $delr = Delr::fromValue(1.0);
        $delc = Delc::fromValue(1.0);
        $laycbd = Laycbd::fromValue(0);
        $top = Top::fromValue(1.0);
        $botm = Botm::fromValue(0);
        $perlen = Perlen::fromValue(1.0);
        $nstp = Nstp::fromInt(1);
        $tsmult = Tsmult::fromValue(1.0);
        $steady = Steady::fromValue(true);
        $itmuni = TimeUnit::fromInt(TimeUnit::DAYS);
        $lenuni = LengthUnit::fromInt(LengthUnit::METERS);
        $extension = Extension::fromString('dis');
        $unitnumber = Unitnumber::fromInteger(11);
        $xul = Xul::fromValue(null);
        $yul = Yul::fromValue(null);
        $rotation = Rotation::fromFloat(0.0);
        $proj4Str = Proj4str::fromString('EPSG:4326');
        $startDateTime = DateTime::fromDateTime(new \DateTime('1/1/1970'));

        $self = new self();
        $self->nlay = $nlay;
        $self->nrow = $nrow;
        $self->ncol = $ncol;
        $self->nper = $nper;
        $self->delr = $delr;
        $self->delc = $delc;
        $self->laycbd = $laycbd;
        $self->top = $top;
        $self->botm = $botm;
        $self->perlen = $perlen;
        $self->nstp = $nstp;
        $self->tsmult = $tsmult;
        $self->steady = $steady;
        $self->itmuni = $itmuni;
        $self->lenuni = $lenuni;
        $self->extension = $extension;
        $self->unitnumber = $unitnumber;
        $self->xul = $xul;
        $self->yul = $yul;
        $self->rotation = $rotation;
        $self->proj4str = $proj4Str;
        $self->startDateTime = $startDateTime;
        return $self;
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param Nlay $nlay
     * @param Nrow $nrow
     * @param Ncol $ncol
     * @param Nper $nper
     * @param Delr $delr
     * @param Delc $delc
     * @param Laycbd $laycbd
     * @param Top $top
     * @param Botm $botm
     * @param Perlen $perlen
     * @param Nstp $nstp
     * @param Tsmult $tsmult
     * @param Steady $steady
     * @param TimeUnit $itmuni
     * @param LengthUnit $lenuni
     * @param Extension $extension
     * @param Unitnumber $unitnumber
     * @param Xul $xul
     * @param Yul $yul
     * @param Rotation $rotation
     * @param Proj4str $proj4Str
     * @param DateTime $startDateTime
     * @return DisPackage
     */
    public static function fromParams(
        Nlay $nlay,
        Nrow $nrow,
        Ncol $ncol,
        Nper $nper,
        Delr $delr,
        Delc $delc,
        Laycbd $laycbd,
        Top $top,
        Botm $botm,
        Perlen $perlen,
        Nstp $nstp,
        Tsmult $tsmult,
        Steady $steady,
        TimeUnit $itmuni,
        LengthUnit $lenuni,
        Extension $extension,
        Unitnumber $unitnumber,
        Xul $xul,
        Yul $yul,
        Rotation $rotation,
        Proj4str $proj4Str,
        DateTime $startDateTime
    ): DisPackage
    {
        $self = new self();
        $self->nlay = $nlay;
        $self->nrow = $nrow;
        $self->ncol = $ncol;
        $self->nper = $nper;
        $self->delr = $delr;
        $self->delc = $delc;
        $self->laycbd = $laycbd;
        $self->top = $top;
        $self->botm = $botm;
        $self->perlen = $perlen;
        $self->nstp = $nstp;
        $self->tsmult = $tsmult;
        $self->steady = $steady;
        $self->itmuni = $itmuni;
        $self->lenuni = $lenuni;
        $self->extension = $extension;
        $self->unitnumber = $unitnumber;
        $self->xul = $xul;
        $self->yul = $yul;
        $self->rotation = $rotation;
        $self->proj4str = $proj4Str;
        $self->startDateTime = $startDateTime;
        return $self;
    }

    /**
     * @param array $arr
     * @return DisPackage
     * @throws \Exception
     */
    public static function fromArray(array $arr): DisPackage
    {
        $nlay = Nlay::fromInt($arr['nlay']);
        $nrow = Nrow::fromInt($arr['nrow']);
        $ncol = Ncol::fromInt($arr['ncol']);
        $nper = Nper::fromInteger($arr['nper']);
        $delr = Delr::fromValue($arr['delr']);
        $delc = Delc::fromValue($arr['delc']);
        $laycbd = Laycbd::fromValue($arr['laycbd']);
        $top = Top::fromValue($arr['top']);
        $botm = Botm::fromValue($arr['botm']);
        $perlen = Perlen::fromValue($arr['perlen']);
        $nstp = Nstp::fromValue($arr['nstp']);
        $tsmult = Tsmult::fromValue($arr['tsmult']);
        $steady = Steady::fromValue($arr['steady']);
        $itmuni = TimeUnit::fromInt($arr['itmuni']);
        $lenuni = LengthUnit::fromInt($arr['lenuni']);
        $extension = Extension::fromString($arr['extension']);
        $unitnumber = Unitnumber::fromInteger($arr['unitnumber']);
        $xul = Xul::fromValue($arr['xul']);
        $yul = Yul::fromValue($arr['yul']);
        $rotation = Rotation::fromFloat($arr['rotation']);
        $proj4Str = Proj4str::fromString($arr['proj4_str']);
        $startDateTime = DateTime::fromAtom($arr['start_datetime']);

        $self = new self();
        $self->nlay = $nlay;
        $self->nrow = $nrow;
        $self->ncol = $ncol;
        $self->nper = $nper;
        $self->delr = $delr;
        $self->delc = $delc;
        $self->laycbd = $laycbd;
        $self->top = $top;
        $self->botm = $botm;
        $self->perlen = $perlen;
        $self->nstp = $nstp;
        $self->tsmult = $tsmult;
        $self->steady = $steady;
        $self->itmuni = $itmuni;
        $self->lenuni = $lenuni;
        $self->extension = $extension;
        $self->unitnumber = $unitnumber;
        $self->xul = $xul;
        $self->yul = $yul;
        $self->rotation = $rotation;
        $self->proj4str = $proj4Str;
        $self->startDateTime = $startDateTime;
        return $self;
    }

    /**
     * @param Nlay $nlay
     * @return DisPackage
     * @throws \Exception
     */
    public function updateNlay(Nlay $nlay): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->nlay = $nlay;
        return $package;
    }

    /**
     * @param Nrow $nrow
     * @return DisPackage
     * @throws \Exception
     */
    public function updateNrow(Nrow $nrow): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->nrow = $nrow;
        return $package;
    }

    /**
     * @param Ncol $ncol
     * @return DisPackage
     * @throws \Exception
     */
    public function updateNcol(Ncol $ncol): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->ncol = $ncol;
        return $package;
    }

    /**
     * @param Nper $nper
     * @return DisPackage
     * @throws \Exception
     */
    public function updateNper(NPer $nper): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->nper = $nper;
        return $package;
    }

    /**
     * @param Delr $delr
     * @return DisPackage
     * @throws \Exception
     */
    public function updateDelr(Delr $delr): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->delr = $delr;
        return $package;
    }

    /**
     * @param Delc $delc
     * @return DisPackage
     * @throws \Exception
     */
    public function updateDelc(Delc $delc): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->delc = $delc;
        return $package;
    }

    /**
     * @param Laycbd $laycbd
     * @return DisPackage
     * @throws \Exception
     */
    public function updateLaycbd(Laycbd $laycbd): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->laycbd = $laycbd;
        return $package;
    }

    /**
     * @param Top $top
     * @return DisPackage
     * @throws \Exception
     */
    public function updateTop(Top $top): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->top = $top;
        return $package;
    }

    /**
     * @param Botm $botm
     * @return DisPackage
     * @throws \Exception
     */
    public function updateBotm(Botm $botm): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->botm = $botm;
        return $package;
    }

    /**
     * @param Perlen $perlen
     * @return DisPackage
     * @throws \Exception
     */
    public function updatePerlen(Perlen $perlen): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->perlen = $perlen;
        return $package;
    }

    /**
     * @param Nstp $nstp
     * @return DisPackage
     * @throws \Exception
     */
    public function updateNstp(Nstp $nstp): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->nstp = $nstp;
        return $package;
    }

    /**
     * @param Tsmult $tsmult
     * @return DisPackage
     * @throws \Exception
     */
    public function updateTsmult(Tsmult $tsmult): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->tsmult = $tsmult;
        return $package;
    }

    /**
     * @param Steady $steady
     * @return DisPackage
     * @throws \Exception
     */
    public function updateSteady(Steady $steady): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->steady = $steady;
        return $package;
    }

    /**
     * @param TimeUnit $timeUnit
     * @return DisPackage
     * @throws \Exception
     */
    public function updateTimeUnit(TimeUnit $timeUnit): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->itmuni = $timeUnit;
        return $package;
    }

    /**
     * @param LengthUnit $lengthUnit
     * @return DisPackage
     * @throws \Exception
     */
    public function updateLengthUnit(LengthUnit $lengthUnit): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->lenuni = $lengthUnit;
        return $package;
    }

    /**
     * @param Extension $extension
     * @return DisPackage
     * @throws \Exception
     */
    public function updateExtension(Extension $extension): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->extension = $extension;
        return $package;
    }

    /**
     * @param Unitnumber $unitnumber
     * @return DisPackage
     * @throws \Exception
     */
    public function updateUnitnumber(Unitnumber $unitnumber): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->unitnumber = $unitnumber;
        return $package;
    }

    /**
     * @param Xul $xul
     * @return DisPackage
     * @throws \Exception
     */
    public function updateXul(Xul $xul): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->xul = $xul;
        return $package;
    }

    /**
     * @param Yul $yul
     * @return DisPackage
     * @throws \Exception
     */
    public function updateYul(Yul $yul): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->yul = $yul;
        return $package;
    }

    /**
     * @param Rotation $rotation
     * @return DisPackage
     * @throws \Exception
     */
    public function updateRotation(Rotation $rotation): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->rotation = $rotation;
        return $package;
    }

    /**
     * @param Proj4str $proj4str
     * @return DisPackage
     * @throws \Exception
     */
    public function updateProj4str(Proj4str $proj4str): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->proj4str = $proj4str;
        return $package;
    }

    /**
     * @param DateTime $datetime
     * @return DisPackage
     * @throws \Exception
     */
    public function updateStartDateTime(DateTime $datetime): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->startDateTime = $datetime;
        return $package;
    }

    /**
     * @noinspection MoreThanThreeArgumentsInspection
     * @param GridSize $gridSize
     * @param BoundingBox $boundingBox
     * @param Distance $dx
     * @param Distance $dy
     * @return DisPackage
     * @throws \Exception
     */
    public function updateGridParameters(GridSize $gridSize, BoundingBox $boundingBox, Distance $dx, Distance $dy): DisPackage
    {
        $package = self::fromArray($this->toArray());
        $package->nrow = Nrow::fromInt($gridSize->nY());
        $package->ncol = Ncol::fromInt($gridSize->nX());
        $package->delr = Delr::fromValue($dy->toFloat()/$gridSize->nY());
        $package->delc = Delc::fromValue($dx->toFloat()/$gridSize->nX());
        $package->xul = Xul::fromValue($boundingBox->xMin());
        $package->yul = Yul::fromValue($boundingBox->yMax());
        return $package;
    }

    /**
     * @return Nrow
     */
    public function nRow(): Nrow
    {
        return $this->nrow;
    }

    /**
     * @return Ncol
     */
    public function nCol(): Ncol
    {
        return $this->ncol;
    }

    /**
     * @return Delr
     */
    public function delR(): Delr
    {
        return $this->delr;
    }

    /**
     * @return Delc
     */
    public function delCol(): Delc
    {
        return $this->delc;
    }

    /**
     * @return Xul
     */
    public function xul(): Xul
    {
        return $this->xul;
    }

    /**
     * @return Yul
     */
    public function yul(): Yul
    {
        return $this->yul;
    }

    /**
     * @return TimeUnit
     */
    public function itmuni(): TimeUnit
    {
        return $this->itmuni;
    }

    /**
     * @return LengthUnit
     */
    public function lenuni(): LengthUnit
    {
        return $this->lenuni;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array(
            'nlay' => $this->nlay->toInt(),
            'nrow' => $this->nrow->toInt(),
            'ncol' => $this->ncol->toInt(),
            'nper' => $this->nper->toInt(),
            'delr' => $this->delr->toValue(),
            'delc' => $this->delc->toValue(),
            'laycbd' => $this->laycbd->toValue(),
            'top' => $this->top->toValue(),
            'botm' => $this->botm->toValue(),
            'perlen' => $this->perlen->toValue(),
            'nstp' => $this->nstp->toValue(),
            'tsmult' => $this->tsmult->toValue(),
            'steady' => $this->steady->toValue(),
            'itmuni' => $this->itmuni->toInt(),
            'lenuni' => $this->lenuni->toInt(),
            'extension' => $this->extension->toValue(),
            'unitnumber' => $this->unitnumber->toValue(),
            'xul' => $this->xul->toValue(),
            'yul' => $this->yul->toValue(),
            'rotation' => $this->rotation->toFloat(),
            'proj4_str' => $this->proj4str->toString(),
            'start_datetime' => $this->startDateTime->toAtom()
        );
    }

    /**
     * @return array
     */
    public function getEditables(): array
    {
        return $this->toArray();
    }

    /**
     * @param array $arr
     */
    public function mergeEditables(array $arr): void
    {}

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
