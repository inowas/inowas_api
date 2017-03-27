<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Grid\BottomElevation;
use Inowas\Common\Grid\ColumnNumber;
use Inowas\Common\Grid\DeltaCol;
use Inowas\Common\Grid\DeltaRow;
use Inowas\Common\Grid\LayCbd;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Grid\Proj4String;
use Inowas\Common\Grid\Rotation;
use Inowas\Common\Grid\RowNumber;
use Inowas\Common\Grid\TopElevation;
use Inowas\Common\Grid\UpperLeftCoordinates;
use Inowas\Common\Modflow\Extension;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\NumberOfTimeSteps;
use Inowas\Common\Modflow\Steady;
use Inowas\Common\Modflow\StressPeriodsLength;
use Inowas\Common\Modflow\TimePeriodsNumber;
use Inowas\Common\Modflow\TimeStepMultiplier;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Modflow\UnitNumber;

class DisPackage implements \JsonSerializable
{
    /** @var  LayerNumber */
    protected $nLay;

    /** @var  RowNumber */
    protected $nRow;

    /** @var  ColumnNumber */
    protected $nCol;

    /** @var  TimePeriodsNumber */
    protected $nPer;

    /** @var  DeltaRow */
    protected $delR;

    /** @var  DeltaCol */
    protected $delC;

    /** @var  LayCbd */
    protected $layCbd;

    /** @var  TopElevation */
    protected $top;

    /** @var  BottomElevation */
    protected $botm;

    /** @var  StressPeriodsLength */
    protected $perlen;

    /** @var  NumberOfTimeSteps */
    protected $nstp;

    /** @var  TimeStepMultiplier */
    protected $tsMult;

    /** @var  Steady */
    protected $steady;

    /** @var  TimeUnit */
    protected $itmUni;

    /** @var  LengthUnit */
    protected $lenUni;

    /** @var  Extension */
    protected $extension;

    /** @var UnitNumber */
    protected $unitNumber;

    /** @var  float */
    protected $xul;

    /** @var  float */
    protected $yul;

    /** @var  Rotation */
    protected $rotation;

    /** @var  Proj4String */
    protected $proj4Str;

    /** @var  DateTime */
    protected $startDateTime;

    public static function fromParams(
        ?LayerNumber $nlay = null,
        ?RowNumber $nrow = null,
        ?ColumnNumber $ncol = null,
        ?TimePeriodsNumber $nper = null,
        ?DeltaRow $delr = null,
        ?DeltaCol $delc = null,
        ?LayCbd $laycbd = null,
        ?TopElevation $top = null,
        ?BottomElevation $botm = null,
        ?StressPeriodsLength $perlen = null,
        ?NumberOfTimeSteps $nstp = null,
        ?TimeStepMultiplier $tsmult = null,
        ?Steady $steady = null,
        ?TimeUnit $itmuni = null,
        ?LengthUnit $lenuni = null,
        ?Extension $extension = null,
        ?UnitNumber $unitnumber = null,
        ?UpperLeftCoordinates $upperLeftCoordinates = null,
        ?Rotation $rotation = null,
        ?Proj4String $proj4Str = null,
        ?DateTime $startDateTime = null
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
        $self->xul = $upperLeftCoordinates->xul();
        $self->yul = $upperLeftCoordinates->yul();
        $self->rotation = $rotation;
        $self->proj4Str = $proj4Str;
        $self->startDateTime = $startDateTime;

        if (is_null($self->nLay)){
            $self->nLay = LayerNumber::fromInteger(1);
        }

        if (is_null($self->nCol)){
            $self->nCol = ColumnNumber::fromInteger(1);
        }

        if (is_null($self->nRow)){
            $self->nRow = RowNumber::fromInteger(1);
        }

        if (is_null($self->nPer)){
            $self->nPer = TimePeriodsNumber::fromInteger(1);
        }

        if (is_null($self->delR)){
            $self->delR = DeltaRow::fromValue(1.0);
        }

        if (is_null($self->delC)){
            $self->delC = DeltaCol::fromValue(1.0);
        }

        if (is_null($self->layCbd)){
            $self->layCbd = LayCbd::fromValue(0);
        }

        if (is_null($self->top)){
            $self->top = TopElevation::fromValue(1.0);
        }

        if (is_null($self->botm)){
            $self->botm = BottomElevation::fromValue(0);
        }

        if (is_null($self->perlen)){
            $self->perlen = StressPeriodsLength::fromValue(1.0);
        }

        if (is_null($self->nstp)){
            $self->nstp = NumberOfTimeSteps::fromInt(1);
        }

        if (is_null($self->tsMult)){
            $self->tsMult = TimeStepMultiplier::fromValue(1.0);
        }

        if (is_null($self->steady)){
            $self->steady = Steady::fromValue(true);
        }

        if (is_null($self->itmUni)){
            $self->itmUni = TimeUnit::fromInt(TimeUnit::DAYS);
        }

        if (is_null($self->lenUni)){
            $self->lenUni = LengthUnit::fromInt(LengthUnit::METERS);
        }

        if (is_null($self->extension)){
            $self->extension = Extension::fromString('dis');
        }

        if (is_null($self->unitNumber)){
            $self->unitNumber = UnitNumber::fromInteger(11);
        }

        if (is_null($self->rotation)){
            $self->rotation = Rotation::fromFloat(0.0);
        }

        if (is_null($self->proj4Str)){
            $self->proj4Str = Proj4String::fromString('EPSG:4326');
        }

        if (is_null($self->startDateTime)){
            $self->startDateTime = DateTime::fromDateTime(new \DateTime('1/1/1970'));
        }

        return $self;
    }

    public static function fromArray(array $arr): DisPackage
    {
        $self = new self();
        $self->nLay = LayerNumber::fromInteger($arr['nlay']);
        $self->nRow = RowNumber::fromInteger($arr['nrow']);
        $self->nCol = ColumnNumber::fromInteger($arr['ncol']);
        $self->nPer = TimePeriodsNumber::fromInteger($arr['nper']);
        $self->delR = DeltaRow::fromValue($arr['delr']);
        $self->delC = DeltaCol::fromValue($arr['delc']);
        $self->layCbd = LayCbd::fromValue($arr['laycbd']);
        $self->top = TopElevation::fromValue($arr['top']);
        $self->botm = BottomElevation::fromValue($arr['botm']);
        $self->perlen = StressPeriodsLength::fromValue($arr['perlen']);
        $self->nstp = NumberOfTimeSteps::fromValue($arr['nstp']);
        $self->tsMult = TimeStepMultiplier::fromValue($arr['tsmult']);
        $self->steady = Steady::fromValue($arr['steady']);
        $self->itmUni = TimeUnit::fromInt($arr['itmuni']);
        $self->lenUni = LengthUnit::fromInt($arr['lenuni']);
        $self->extension = Extension::fromString($arr['extension']);
        $self->unitNumber = UnitNumber::fromInteger($arr['unitnumber']);
        $self->xul = $arr['xul'];
        $self->yul = $arr['yul'];
        $self->rotation = Rotation::fromFloat($arr['rotation']);
        $self->proj4Str = Proj4String::fromString($arr['proj4_str']);
        $self->startDateTime = DateTime::fromAtom($arr['start_datetime']);
        return $self;
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
            "extension" => $this->extension->toString(),
            "unitnumber" => $this->unitNumber->toInteger(),
            "xul" => $this->xul,
            "yul" => $this->yul,
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
