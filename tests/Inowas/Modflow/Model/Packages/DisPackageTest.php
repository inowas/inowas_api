<?php

namespace Tests\Inowas\Modflow\Model\Packages;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Grid\BottomElevation;
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
use Inowas\Common\Grid\TopElevation;
use Inowas\Common\Modflow\Extension;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\NumberOfTimeSteps;
use Inowas\Common\Modflow\Steady;
use Inowas\Common\Modflow\StressPeriodsLength;
use Inowas\Common\Modflow\TimePeriodsNumber;
use Inowas\Common\Modflow\TimeStepMultiplier;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Modflow\UnitNumber;
use Inowas\Common\Modflow\Xul;
use Inowas\Common\Modflow\Yul;
use Inowas\Modflow\Model\Packages\DisPackage;


class DisPackageTest extends \PHPUnit_Framework_TestCase
{

    public function setUp(){

    }

    public function test_create_from_params(){

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
        $xul = Xul::fromValue(null);
        $yul = Yul::fromValue(null);
        $rotation = Rotation::fromFloat(0.0);
        $proj4Str = Proj4String::fromString('EPSG:4326');
        $startDateTime = DateTime::fromDateTime(new \DateTime('1/1/1970'));

        $disPackage = DisPackage::fromParams(
            $nLay, $nRow, $nCol, $nPer, $delR, $delC, $layCbd, $top,
            $botm, $perlen, $nstp, $tsMult, $steady, $itmUni, $lenUni,
            $extension, $unitNumber, $xul, $yul, $rotation, $proj4Str, $startDateTime
        );

        $this->assertInstanceOf(DisPackage::class, $disPackage);
        $json = json_encode($disPackage);
        $this->assertJson($json);
    }

    public function test_create_from_defaults(){
        $disPackage = DisPackage::fromDefaults();
        $this->assertInstanceOf(DisPackage::class, $disPackage);
        $json = json_encode($disPackage);
        $this->assertJson($json);
    }

    public function test_update_time_length_units(){

        /** @var DisPackage $disPackage */
        $disPackage = DisPackage::fromDefaults();
        $this->assertEquals(TimeUnit::fromValue(TimeUnit::DAYS), $disPackage->itmuni());
        $this->assertEquals(LengthUnit::fromValue(LengthUnit::METERS), $disPackage->lenuni());

        $newTimeUnit = TimeUnit::fromValue(TimeUnit::SECONDS);
        $disPackage = $disPackage->updateTimeUnit($newTimeUnit);
        $this->assertEquals($newTimeUnit, $disPackage->itmuni());

        $newLengthUnit = LengthUnit::fromValue(LengthUnit::FEET);
        $disPackage = $disPackage->updateLengthUnit($newLengthUnit);
        $this->assertEquals($newLengthUnit, $disPackage->lenuni());
    }

    public function test_update_grid_parameters_units(){

        /** @var DisPackage $disPackage */
        $disPackage = DisPackage::fromDefaults();
        $boundingBox = BoundingBox::fromCoordinates(1,2,3,4,4265, 700, 1200);
        $gridSize = GridSize::fromXY(7, 12);
        $disPackage = $disPackage->updateGridParameters($gridSize, $boundingBox);

        $expectedNRow = RowNumber::fromInteger(12);
        $expectedNCol = ColumnNumber::fromInteger(7);
        $expectedDelR = DeltaRow::fromValue(100);
        $expectedDelC = DeltaCol::fromValue(100);
        $expectedXul = Xul::fromValue(1);
        $expectedYul = Yul::fromValue(4);

        $this->assertEquals($expectedNRow, $disPackage->nRow());
        $this->assertEquals($expectedNCol, $disPackage->nCol());
        $this->assertEquals($expectedDelR, $disPackage->delR());
        $this->assertEquals($expectedDelC, $disPackage->delCol());
        $this->assertEquals($expectedXul, $disPackage->xul());
        $this->assertEquals($expectedYul, $disPackage->yul());
        $json = json_encode($disPackage);
        $this->assertJson($json);
    }
}
