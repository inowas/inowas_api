<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Service;

use Inowas\Common\Boundaries\ConstantHeadBoundary;
use Inowas\Common\Boundaries\ConstantHeadDateTimeValue;
use Inowas\Common\Boundaries\DateTimeValue;
use Inowas\Common\Boundaries\GeneralHeadBoundary;
use Inowas\Common\Boundaries\GeneralHeadDateTimeValue;
use Inowas\Common\Boundaries\GridCellDateTimeValues;
use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Boundaries\RechargeBoundary;
use Inowas\Common\Boundaries\RiverBoundary;
use Inowas\Common\Boundaries\RiverDateTimeValue;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Geometry\LineString;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Modflow\Rech;
use Inowas\Common\Modflow\StressPeriod;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Model\Exception\InvalidTimeUnitException;
use Inowas\ModflowModel\Model\Packages\ChdStressPeriodData;
use Inowas\ModflowModel\Model\Packages\ChdStressPeriodGridCellValue;
use Inowas\ModflowModel\Model\Packages\GhbStressPeriodData;
use Inowas\ModflowModel\Model\Packages\GhbStressPeriodGridCellValue;
use Inowas\ModflowModel\Model\Packages\RchStressPeriodData;
use Inowas\ModflowModel\Model\Packages\RchStressPeriodValue;
use Inowas\ModflowModel\Model\Packages\RivStressPeriodData;
use Inowas\ModflowModel\Model\Packages\RivStressPeriodGridCellValue;
use Inowas\ModflowModel\Model\Packages\WelStressPeriodData;
use Inowas\ModflowModel\Model\Packages\WelStressPeriodGridCellValue;

class StressPeriodDataGenerator
{

    /** @var GeoTools */
    protected $geoTools;

    public function __construct(GeoTools $geoTools){
        $this->geoTools = $geoTools;
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param array $chdBoundaries
     * @param StressPeriods $stressPeriods
     * @param GridSize $gridSize
     * @param BoundingBox $boundingBox
     * @return ChdStressPeriodData
     */
    public function fromConstantHeadBoundaries(array $chdBoundaries, StressPeriods $stressPeriods, GridSize $gridSize, BoundingBox $boundingBox): ChdStressPeriodData
    {
        $startTime = $stressPeriods->start();
        $timeUnit = $stressPeriods->timeUnit();

        $chdSpd = ChdStressPeriodData::create();
        /** @var ConstantHeadBoundary $chdBoundary */
        foreach ($chdBoundaries as $chdBoundary) {
            if (! $chdBoundary instanceof ConstantHeadBoundary) {
                continue;
            }

            /** @var GridCellDateTimeValues[] $gridCellDateTimeValues */
            $gridCellDateTimeValues = $this->calculateGridCellDateTimeValues($chdBoundary, $gridSize, $boundingBox);
            foreach ($gridCellDateTimeValues as $gridCellDateTimeValue) {

                /** @var StressPeriod $stressperiod */
                foreach ($stressPeriods->stressperiods() as $stressperiod) {
                    $totim = TotalTime::fromInt($stressperiod->totimStart());
                    $sp = $stressPeriods->spNumberFromTotim($totim);
                    $dateTimeValue = $gridCellDateTimeValue->findValueByDateTime($this->calculateDateTimeFromTotim($startTime, $totim, $timeUnit));
                    if ($dateTimeValue instanceof ConstantHeadDateTimeValue){
                        $chdSpd->addGridCellValue(ChdStressPeriodGridCellValue::fromParams($sp, $gridCellDateTimeValue->layer(), $gridCellDateTimeValue->row(), $gridCellDateTimeValue->column(),$dateTimeValue->shead(), $dateTimeValue->ehead()));
                    }
                }
            }
        }

        return $chdSpd;
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param array $ghbBoundaries
     * @param StressPeriods $stressPeriods
     * @param GridSize $gridSize
     * @param BoundingBox $boundingBox
     * @return GhbStressPeriodData
     */
    public function fromGeneralHeadBoundaries(array $ghbBoundaries, StressPeriods $stressPeriods, GridSize $gridSize, BoundingBox $boundingBox): GhbStressPeriodData
    {
        $startTime = $stressPeriods->start();
        $timeUnit = $stressPeriods->timeUnit();

        $ghbSpd = GhbStressPeriodData::create();
        /** @var GeneralHeadBoundary $ghbBoundary */
        foreach ($ghbBoundaries as $ghbBoundary) {
            if (! $ghbBoundary instanceof GeneralHeadBoundary) {
                continue;
            }

            /** @var GridCellDateTimeValues[] $gridCellDateTimeValues */
            $gridCellDateTimeValues = $this->calculateGridCellDateTimeValues($ghbBoundary, $gridSize, $boundingBox);
            foreach ($gridCellDateTimeValues as $gridCellDateTimeValue) {

                /** @var StressPeriod $stressperiod */
                foreach ($stressPeriods->stressperiods() as $stressperiod) {
                    $totim = TotalTime::fromInt($stressperiod->totimStart());
                    $sp = $stressPeriods->spNumberFromTotim($totim);
                    $dateTimeValue = $gridCellDateTimeValue->findValueByDateTime($this->calculateDateTimeFromTotim($startTime, $totim, $timeUnit));
                    if ($dateTimeValue instanceof GeneralHeadDateTimeValue){
                        $ghbSpd->addGridCellValue(GhbStressPeriodGridCellValue::fromParams($sp, $gridCellDateTimeValue->layer(), $gridCellDateTimeValue->row(), $gridCellDateTimeValue->column(), $dateTimeValue->stage(), $dateTimeValue->cond()));
                    }
                }
            }
        }

        return $ghbSpd;
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param array $rivBoundaries
     * @param StressPeriods $stressPeriods
     * @param GridSize $gridSize
     * @param BoundingBox $boundingBox
     * @return RivStressPeriodData
     */
    public function fromRiverBoundaries(array $rivBoundaries, StressPeriods $stressPeriods, GridSize $gridSize, BoundingBox $boundingBox): RivStressPeriodData
    {
        $startTime = $stressPeriods->start();
        $timeUnit = $stressPeriods->timeUnit();

        $rivSpd = RivStressPeriodData::create();

        /** @var RiverBoundary $rivBoundary */
        foreach ($rivBoundaries as $rivBoundary) {
            if (! $rivBoundary instanceof RiverBoundary) {
                continue;
            }

            /** @var GridCellDateTimeValues[] $gridCellDateTimeValues */
            $gridCellDateTimeValues = $this->calculateGridCellDateTimeValues($rivBoundary, $gridSize, $boundingBox);
            foreach ($gridCellDateTimeValues as $gridCellDateTimeValue) {

                /** @var StressPeriod $stressperiod */
                foreach ($stressPeriods->stressperiods() as $stressperiod) {
                    $totim = TotalTime::fromInt($stressperiod->totimStart());
                    $sp = $stressPeriods->spNumberFromTotim($totim);
                    $dateTimeValue = $gridCellDateTimeValue->findValueByDateTime($this->calculateDateTimeFromTotim($startTime, $totim, $timeUnit));
                    if ($dateTimeValue instanceof RiverDateTimeValue){
                        $rivSpd->addGridCellValue(RivStressPeriodGridCellValue::fromParams($sp, $gridCellDateTimeValue->layer(), $gridCellDateTimeValue->row(), $gridCellDateTimeValue->column(),$dateTimeValue->stage(), $dateTimeValue->cond(), $dateTimeValue->rbot()));
                    }
                }
            }
        }

        return $rivSpd;
    }

    public function fromRechargeBoundaries(array $rchBoundaries, StressPeriods $stressPeriods): RchStressPeriodData
    {
        $startTime = $stressPeriods->start();
        $timeUnit = $stressPeriods->timeUnit();
        $rchSpd = RchStressPeriodData::create();

        /** @var RechargeBoundary $rchBoundary */
        foreach ($rchBoundaries as $rchBoundary) {
            if (! $rchBoundary instanceof RechargeBoundary) {
                continue;
            }

            $activeCells = $rchBoundary->activeCells();


            /** @var StressPeriod $stressperiod */
            foreach ($stressPeriods->stressperiods() as $stressperiod) {
                $totim = TotalTime::fromInt($stressperiod->totimStart());
                $sp = $stressPeriods->spNumberFromTotim($totim);

                $cells = $activeCells->fullArray();
                $rechargeValue = $rchBoundary->findValueByDateTime($this->calculateDateTimeFromTotim($startTime, $totim, $timeUnit));
                $rech = [];
                foreach ($cells as $rowKey => $row){
                    $rech[$rowKey] = [];
                    foreach ($row as $colKey => $value){
                        $rech[$rowKey][$colKey] = 0;
                        if ($value === true){
                            $rech[$rowKey][$colKey] = $rechargeValue->rechargeRate();
                        }
                    }
                }

                $rchSpd->addStressPeriodValue(RchStressPeriodValue::fromParams($sp, Rech::fromValue($rech)));
            }
        }

        return $rchSpd;
    }

    public function fromWellBoundaries(array $wellBoundaries, StressPeriods $stressPeriods): WelStressPeriodData
    {
        $startTime = $stressPeriods->start();
        $timeUnit = $stressPeriods->timeUnit();

        $wspd = WelStressPeriodData::create();

        /** @var WellBoundary[] $wellBoundary */
        foreach ($wellBoundaries as $wellBoundary){
            if (! $wellBoundary instanceof WellBoundary){
                continue;
            }

            /** @var StressPeriod $stressperiod */
            foreach ($stressPeriods->stressperiods() as $stressperiod) {
                $totim = TotalTime::fromInt($stressperiod->totimStart());
                $sp = $stressPeriods->spNumberFromTotim($totim);

                /** @var ActiveCells $activeCells */
                $activeCells = $wellBoundary->activeCells();

                $cells = $activeCells->cells();
                if (count($cells)>0){
                    $cell = $cells[0];
                    $pumpingRate = $wellBoundary->findValueByDateTime($this->calculateDateTimeFromTotim($startTime, $totim, $timeUnit));
                    $wspd->addGridCellValue(WelStressPeriodGridCellValue::fromParams($sp, $cell[0], $cell[1], $cell[2], $pumpingRate->pumpingRate()));
                }
            }
        }

        return $wspd;
    }

    protected function calculateGridCellDateTimeValues(ModflowBoundary $boundary, GridSize $gridSize, BoundingBox $boundingBox): array
    {
        $gridCellDateTimeValues = [];
        $observationPoints = $boundary->observationPoints();

        if (count($observationPoints) === 0) {
            throw new \Exception();
        }

        if (count($observationPoints) === 1) {
            // no interpolation is necessary

            /** @var ObservationPoint $observationPoint */
            $observationPoint = array_values($observationPoints)[0];

            /** @var DateTimeValue[] $dateTimeValues */
            $dateTimeValues = $observationPoint->dateTimeValues();
            $activeCells = $boundary->activeCells();
            $cells = $activeCells->cells();

            foreach ($cells as $cell){
                $gridCellDateTimeValues[] = GridCellDateTimeValues::fromParams($cell[0], $cell[1], $cell[2], $dateTimeValues);
            }
        }

        if (count($observationPoints) > 1) {

            $geometry = $boundary->geometry();
            if (! $geometry->value() instanceof LineString){
                throw new \Exception();
            }

            $gridCellDateTimeValues = $this->geoTools->interpolateGridCellDateTimeValuesFromLinestringAndObservationPoints($geometry->value(), $observationPoints, $boundary->activeCells(), $boundingBox, $gridSize);
        }

        return $gridCellDateTimeValues;
    }

    protected function calculateDateTimeFromTotim(DateTime $start, TotalTime $totalTime, TimeUnit $timeUnit): \DateTimeImmutable
    {
        $dateTime = clone $start->toDateTime();

        if ($timeUnit->toInt() === $timeUnit::SECONDS){
            $dateTime->modify(sprintf('+%s seconds', $totalTime->toInteger()));
            return \DateTimeImmutable::createFromMutable($dateTime);
        }

        if ($timeUnit->toInt() === $timeUnit::MINUTES){
            $dateTime->modify(sprintf('+%s minutes', $totalTime->toInteger()));
            return \DateTimeImmutable::createFromMutable($dateTime);
        }

        if ($timeUnit->toInt() === $timeUnit::HOURS){
            $dateTime->modify(sprintf('+%s hours', $totalTime->toInteger()));
            return \DateTimeImmutable::createFromMutable($dateTime);
        }

        if ($timeUnit->toInt() === $timeUnit::DAYS){
            $dateTime->modify(sprintf('+%s days', $totalTime->toInteger()));
            return \DateTimeImmutable::createFromMutable($dateTime);
        }

        throw InvalidTimeUnitException::withTimeUnitAndAvailableTimeUnits($timeUnit, $timeUnit->availableTimeUnits);
    }
}
