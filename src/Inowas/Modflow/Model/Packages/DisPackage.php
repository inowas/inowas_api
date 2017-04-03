<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Modflow\Botm;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\ColumnNumber;
use Inowas\Common\Grid\DeltaCol;
use Inowas\Common\Grid\DeltaRow;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Grid\LayCbd;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Grid\Proj4String;
use Inowas\Common\Grid\Rotation;
use Inowas\Common\Grid\RowNumber;
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

class DisPackage implements PackageInterface
{

    /** @var string  */
    protected $type = 'dis';

    /** @var  LayerNumber */
    protected $nLay;

    /** @var  RowNumber */
    protected $nRow;

    /** @var  ColumnNumber */
    protected $nCol;

    /** @var  Nper */
    protected $nPer;

    /** @var  DeltaRow */
    protected $delR;

    /** @var  DeltaCol */
    protected $delC;

    /** @var  LayCbd */
    protected $layCbd;

    /** @var  Top */
    protected $top;

    /** @var  Botm */
    protected $botm;

    /** @var  Perlen */
    protected $perlen;

    /** @var  Nstp */
    protected $nstp;

    /** @var  Tsmult */
    protected $tsMult;

    /** @var  Steady */
    protected $steady;

    /** @var  TimeUnit */
    protected $itmUni;

    /** @var  LengthUnit */
    protected $lenUni;

    /** @var  Extension */
    protected $extension;

    /** @var Unitnumber */
    protected $unitNumber;

    /** @var  Xul */
    protected $xul;

    /** @var  Yul */
    protected $yul;

    /** @var  Rotation */
    protected $rotation;

    /** @var  Proj4String */
    protected $proj4Str;

    /** @var  DateTime */
    protected $startDateTime;

    public static function fromDefaults(): DisPackage
    {
        // DEFAULT
        $nlay = LayerNumber::fromInteger(1);
        $ncol = ColumnNumber::fromInteger(1);
        $nrow = RowNumber::fromInteger(1);
        $nper = Nper::fromInteger(1);
        $delr = DeltaRow::fromValue(1.0);
        $delc = DeltaCol::fromValue(1.0);
        $laycbd = LayCbd::fromValue(0);
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
        $proj4Str = Proj4String::fromString('EPSG:4326');
        $startDateTime = DateTime::fromDateTime(new \DateTime('1/1/1970'));

        $self = new self();
        $self->nLay = $nlay;
        $self->nRow = $nrow;
        $self->nCol = $ncol;
        $self->nPer = $nper;
        $self->delR = $delr;
        $self->delC = $delc;
        $self->layCbd = $laycbd;
        $self->top = $top;
        $self->botm = $botm;
        $self->perlen = $perlen;
        $self->nstp = $nstp;
        $self->tsMult = $tsmult;
        $self->steady = $steady;
        $self->itmUni = $itmuni;
        $self->lenUni = $lenuni;
        $self->extension = $extension;
        $self->unitNumber = $unitnumber;
        $self->xul = $xul;
        $self->yul = $yul;
        $self->rotation = $rotation;
        $self->proj4Str = $proj4Str;
        $self->startDateTime = $startDateTime;
        return $self;
    }

    public static function fromParams(
        LayerNumber $nlay,
        RowNumber $nrow,
        ColumnNumber $ncol,
        Nper $nper,
        DeltaRow $delr,
        DeltaCol $delc,
        LayCbd $laycbd,
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
        Proj4String $proj4Str,
        DateTime $startDateTime
    ): DisPackage
    {
        $self = new self();
        $self->nLay = $nlay;
        $self->nRow = $nrow;
        $self->nCol = $ncol;
        $self->nPer = $nper;
        $self->delR = $delr;
        $self->delC = $delc;
        $self->layCbd = $laycbd;
        $self->top = $top;
        $self->botm = $botm;
        $self->perlen = $perlen;
        $self->nstp = $nstp;
        $self->tsMult = $tsmult;
        $self->steady = $steady;
        $self->itmUni = $itmuni;
        $self->lenUni = $lenuni;
        $self->extension = $extension;
        $self->unitNumber = $unitnumber;
        $self->xul = $xul;
        $self->yul = $yul;
        $self->rotation = $rotation;
        $self->proj4Str = $proj4Str;
        $self->startDateTime = $startDateTime;
        return $self;
    }

    public static function fromArray(array $arr): DisPackage
    {
        $nlay = LayerNumber::fromInteger($arr['nlay']);
        $nrow = RowNumber::fromInteger($arr['nrow']);
        $ncol = ColumnNumber::fromInteger($arr['ncol']);
        $nper = Nper::fromInteger($arr['nper']);
        $delr = DeltaRow::fromValue($arr['delr']);
        $delc = DeltaCol::fromValue($arr['delc']);
        $laycbd = LayCbd::fromValue($arr['laycbd']);
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
        $proj4Str = Proj4String::fromString($arr['proj4_str']);
        $startDateTime = DateTime::fromAtom($arr['start_datetime']);

        $self = new self();
        $self->nLay = $nlay;
        $self->nRow = $nrow;
        $self->nCol = $ncol;
        $self->nPer = $nper;
        $self->delR = $delr;
        $self->delC = $delc;
        $self->layCbd = $laycbd;
        $self->top = $top;
        $self->botm = $botm;
        $self->perlen = $perlen;
        $self->nstp = $nstp;
        $self->tsMult = $tsmult;
        $self->steady = $steady;
        $self->itmUni = $itmuni;
        $self->lenUni = $lenuni;
        $self->extension = $extension;
        $self->unitNumber = $unitnumber;
        $self->xul = $xul;
        $self->yul = $yul;
        $self->rotation = $rotation;
        $self->proj4Str = $proj4Str;
        $self->startDateTime = $startDateTime;
        return $self;
    }

    public function updateTimeUnit(TimeUnit $timeUnit): DisPackage
    {
        $this->itmUni = $timeUnit;
        return self::fromArray($this->toArray());
    }

    public function updateLengthUnit(LengthUnit $lengthUnit): DisPackage
    {
        $this->lenUni = $lengthUnit;
        return self::fromArray($this->toArray());
    }

    public function updateGridParameters(GridSize $gridSize, BoundingBox $boundingBox): DisPackage
    {
        $this->nRow = RowNumber::fromInteger($gridSize->nY());
        $this->nCol = ColumnNumber::fromInteger($gridSize->nX());
        $this->delR = DeltaRow::fromValue($boundingBox->dY()/$gridSize->nY());
        $this->delC = DeltaCol::fromValue($boundingBox->dX()/$gridSize->nX());
        $this->xul = Xul::fromValue($boundingBox->xMin());
        $this->yul = Yul::fromValue($boundingBox->yMax());
        return self::fromArray($this->toArray());
    }

    public function updateStartDateTime(DateTime $start): DisPackage
    {
        $this->startDateTime = $start;
        return self::fromArray($this->toArray());
    }

    public function updateNper(Nper $nper): DisPackage
    {
        $this->nPer = $nper;
        return self::fromArray($this->toArray());
    }

    public function updatePerlen(Perlen $perlen): DisPackage
    {
        $this->perlen = $perlen;
        return self::fromArray($this->toArray());
    }

    public function updateNstp(Nstp $nstp): DisPackage
    {
        $this->nstp = $nstp;
        return self::fromArray($this->toArray());
    }

    public function updateTsmult(Tsmult $tsmult): DisPackage
    {
        $this->tsMult = $tsmult;
        return self::fromArray($this->toArray());
    }

    public function updateSteady(Steady $steady): DisPackage
    {
        $this->steady = $steady;
        return self::fromArray($this->toArray());
    }

    public function type(): string
    {
        return $this->type;
    }

    public function nRow(): RowNumber
    {
        return $this->nRow;
    }

    public function nCol(): ColumnNumber
    {
        return $this->nCol;
    }

    public function delR(): DeltaRow
    {
        return $this->delR;
    }

    public function delCol(): DeltaCol
    {
        return $this->delC;
    }

    public function xul(): Xul
    {
        return $this->xul;
    }

    public function yul(): Yul
    {
        return $this->yul;
    }

    public function itmuni(): TimeUnit
    {
        return $this->itmUni;
    }

    public function lenuni(): LengthUnit
    {
        return $this->lenUni;
    }

    public function toArray(): array
    {
        return array(
            "nlay" => $this->nLay->toInteger(),
            "nrow" => $this->nRow->toInteger(),
            "ncol" => $this->nCol->toInteger(),
            "nper" => $this->nPer->toInteger(),
            "delr" => $this->delR->toValue(),
            "delc" => $this->delC->toValue(),
            "laycbd" => $this->layCbd->toValue(),
            "top" => $this->top->toValue(),
            "botm" => $this->botm->toValue(),
            "perlen" => $this->perlen->toValue(),
            "nstp" => $this->nstp->toValue(),
            "tsmult" => $this->tsMult->toValue(),
            "steady" => $this->steady->toValue(),
            "itmuni" => $this->itmUni->toInt(),
            "lenuni" => $this->lenUni->toInt(),
            "extension" => $this->extension->toValue(),
            "unitnumber" => $this->unitNumber->toValue(),
            "xul" => $this->xul->toValue(),
            "yul" => $this->yul->toValue(),
            "rotation" => $this->rotation->toFloat(),
            "proj4_str" => $this->proj4Str->toString(),
            "start_datetime" => $this->startDateTime->toAtom()
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
