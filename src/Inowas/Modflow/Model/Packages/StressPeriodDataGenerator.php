<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

use Inowas\Common\Boundaries\GeneralHeadBoundary;
use Inowas\Common\Boundaries\GeneralHeadDateTimeValue;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Modflow\StressPeriod;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\Exception\InvalidTimeUnitException;

class StressPeriodDataGenerator
{

    public function __construct(){}

    public function fromGeneralHeadBoundaries(array $ghbBoundaries, StressPeriods $stressPeriods, GridSize $gridSize, BoundingBox $boundingBox): GhbStressPeriodData
    {

        $start = $stressPeriods->start();
        $timeUnit = $stressPeriods->timeUnit();

        $ghbSpd = GhbStressPeriodData::create();
        /** @var StressPeriod $stressperiod */
        foreach ($stressPeriods->stressperiods() as $stressperiod) {
            $totim = TotalTime::fromInt($stressperiod->totimStart());
            $sp = $stressPeriods->spNumberFromTotim($totim);

            /** @var GeneralHeadBoundary $ghbBoundary */
            foreach ($ghbBoundaries as $ghbBoundary) {
                if (! $ghbBoundary instanceof GeneralHeadBoundary) {
                    continue;
                }

                $cells = $this->interpolateGhb($ghbBoundary, $start, $totim, $timeUnit);
                foreach ($cells as $cell){
                    $dateTimeValue = $ghbBoundary->findValueByDateTime(self::calculateDateTimeFromTotim($start, $totim, $timeUnit));
                    if ($dateTimeValue instanceof GeneralHeadDateTimeValue){
                        $ghbSpd->addGridCellValue(GhbStressPeriodGridCellValue::fromParams($sp, $cell[0], $cell[1], $cell[2], $cell[3], $cell[4]));
                    }
                }
            }
        }

        return $ghbSpd;
    }

    /*
     * Returns an array with gridCellValues
     * [
     *  [$lay, $row, $col, stage, cond],
     *  [$lay, $row, $col, stage, cond],
     *  [$lay, $row, $col, stage, cond],
     *  [$lay, $row, $col, stage, cond],
     *  [$lay, $row, $col, stage, cond],
     * ]
     */
    private function interpolateGhb(GeneralHeadBoundary $generalHeadBoundary, DateTime $start, TotalTime $totim, TimeUnit $timeUnit): array
    {
        $observationPoints = $generalHeadBoundary->observationPoints();

        if (count($observationPoints) == 0) {
            throw new \Exception();
        }

        if (count($observationPoints) == 1) {
            // no interpolation is necessary

            /** @var ObservationPoint $observationPoint */
            $observationPoint = array_values($observationPoints)[0];
            $dateTime = $this->calculateDateTimeFromTotim($start, $totim, $timeUnit);
            $dateTimeValue = $generalHeadBoundary->findValueByDateTimeAndObservationPointId($dateTime, $observationPoint->id());

            $result = [];

            $cells = $generalHeadBoundary->activeCells()->cells();
            foreach ($cells as $cell){
                $result[] = array($cell[0], $cell[1], $cell[2], $dateTimeValue->stage(), $dateTimeValue->cond());
            }

            return $result;
        }

        if (count($observationPoints) > 1) {
            // @todo implement interpolation

            /** @var ObservationPoint $observationPoint */
            $observationPoint = array_values($observationPoints)[0];
            $dateTime = $this->calculateDateTimeFromTotim($start, $totim, $timeUnit);
            $dateTimeValue = $generalHeadBoundary->findValueByDateTimeAndObservationPointId($dateTime, $observationPoint->id());

            $result = [];

            $cells = $generalHeadBoundary->activeCells()->cells();
            foreach ($cells as $cell){
                $result[] = array($cell[0], $cell[1], $cell[2], $dateTimeValue->stage(), $dateTimeValue->cond());
            }

            return $result;
        }



        return [];
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
