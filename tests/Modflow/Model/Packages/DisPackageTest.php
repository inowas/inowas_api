<?php

namespace Tests\Inowas\Modflow\Model\Packages;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Geometry\Point;
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
use Inowas\Modflow\Model\Packages\DisPackage;


class DisPackageTest extends \PHPUnit_Framework_TestCase
{
    public function test_create(){

        // DEFAULTS
        $nLay = LayerNumber::fromInteger(1);
        $nCol = ColumnNumber::fromInteger(1);
        $nRow = RowNumber::fromInteger(1);
        $nPer = TimePeriodsNumber::fromInteger(1);
        $delR = DeltaRow::fromValue(1.0);
        $delC = DeltaCol::fromValue(1.0);
        $layCbd = LayCbd::fromValue(0);
        $top = TopElevation::fromValue(1.0);
        $botm = BottomElevation::fromValue(0);
        $perlen = StressPeriodsLength::fromValue(1.0);
        $nstp = NumberOfTimeSteps::fromInt(1);
        $tsMult = TimeStepMultiplier::fromValue(1.0);
        $steady = Steady::fromValue(true);
        $itmUni = TimeUnit::fromInt(TimeUnit::DAYS);
        $lenUni = LengthUnit::fromInt(LengthUnit::METERS);
        $extension = Extension::fromString('dis');
        $unitNumber = UnitNumber::fromInteger(11);
        $upperLeftCoordinates = UpperLeftCoordinates::fromPoint(new Point(1,2));
        $rotation = Rotation::fromFloat(0.0);
        $proj4Str = Proj4String::fromString('EPSG:4326');
        $startDateTime = DateTime::fromDateTime(new \DateTime('1/1/1970'));

        $disPackage = DisPackage::fromParams(
            $nLay, $nRow, $nCol, $nPer, $delR, $delC, $layCbd, $top,
            $botm, $perlen, $nstp, $tsMult, $steady, $itmUni, $lenUni,
            $extension, $unitNumber, $upperLeftCoordinates, $rotation, $proj4Str, $startDateTime
        );

        $this->assertInstanceOf(DisPackage::class, $disPackage);
        $json = json_encode($disPackage);
        $this->assertJson($json);
        var_dump($json);
    }
}
