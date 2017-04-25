<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Service;

use Inowas\Common\Boundaries\DateTimeValue;
use Inowas\Common\Boundaries\GeneralHeadBoundary;
use Inowas\Common\Boundaries\GeneralHeadDateTimeValue;
use Inowas\Common\Boundaries\GridCellDateTimeValues;
use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Geometry\LineString;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Modflow\StressPeriod;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\GeoTools\Model\GeoTools;
use Inowas\Modflow\Model\Exception\InvalidTimeUnitException;
use Inowas\Modflow\Model\Packages\GhbStressPeriodData;
use Inowas\Modflow\Model\Packages\GhbStressPeriodGridCellValue;

class StressPeriodDataGenerator
{

    /** @var GeoTools */
    protected $geoTools;

    public function __construct(GeoTools $geoTools){
        $this->geoTools = $geoTools;
    }

    public function fromGeneralHeadBoundaries(array $ghbBoundaries, StressPeriods $stressPeriods, GridSize $gridSize, BoundingBox $boundingBox): GhbStressPeriodData
    {
        $startTime = $stressPeriods->start();
        $timeUnit = $stressPeriods->timeUnit();

        $ghbSpd = GhbStressPeriodData::create();
        foreach ($ghbBoundaries as $ghbBoundary) {
            if (!$ghbBoundary instanceof GeneralHeadBoundary) {
                continue;
            }

            /** @var StressPeriod $stressperiod */
            foreach ($stressPeriods->stressperiods() as $stressperiod) {
                $totim = TotalTime::fromInt($stressperiod->totimStart());
                $sp = $stressPeriods->spNumberFromTotim($totim);
            }
        }

        /** @var GeneralHeadBoundary $ghbBoundary */
        foreach ($ghbBoundaries as $ghbBoundary) {
            if (! $ghbBoundary instanceof GeneralHeadBoundary) {
                continue;
            }

            /** @var GridCellDateTimeValues[] $gridCellDateTimeValues */
            $gridCellDateTimeValues = $this->calculateGridCellDateTimeValues($ghbBoundary, $gridSize, $boundingBox);

            foreach ($gridCellDateTimeValues as $gridCellDateTimeValue) {
                foreach ($stressPeriods->stressperiods() as $stressperiod) {
                    $totim = TotalTime::fromInt($stressperiod->totimStart());
                    $sp = $stressPeriods->spNumberFromTotim($totim);
                    $dateTimeValue = $gridCellDateTimeValue->findValueByDateTime($this->calculateDateTimeFromTotim($startTime, $totim, $timeUnit));
                    if ($dateTimeValue instanceof GeneralHeadDateTimeValue){
                        $ghbSpd->addGridCellValue(GhbStressPeriodGridCellValue::fromParams($sp, $gridCellDateTimeValue->layer(), $gridCellDateTimeValue->row(), $gridCellDateTimeValue->column(),$dateTimeValue->cond(), $dateTimeValue->stage()));
                    }
                }
            }
        }

        return $ghbSpd;
    }

    private function calculateGridCellDateTimeValues(ModflowBoundary $boundary, GridSize $gridSize, BoundingBox $boundingBox): array
    {
        $gridCellDateTimeValues = [];
        $observationPoints = $boundary->observationPoints();

        if (count($observationPoints) == 0) {
            throw new \Exception();
        }

        if (count($observationPoints) == 1) {
            // no interpolation is necessary

            /** @var ObservationPoint $observationPoint */
            $observationPoint = array_values($observationPoints)[0];

            /** @var DateTimeValue[] $dateTimeValues */
            $dateTimeValues = $observationPoint->dateTimeValues();
            $cells = $boundary->activeCells()->cells();

            foreach ($cells as $cell){
                foreach ($dateTimeValues as $dateTimeValue){
                    $gridCellDateTimeValues[] = GridCellDateTimeValues::fromParams($cell[0], $cell[1], $cell[2], $dateTimeValue);
                }
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
