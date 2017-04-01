<?php

namespace Inowas\Modflow\Model\Service;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\StressPeriod;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\Exception\InvalidTimeUnitException;
use Inowas\Modflow\Projection\BoundaryList\BoundaryFinder;

class ModflowModelManager implements ModflowModelManagerInterface
{

    /** @var  BoundaryFinder */
    protected $boundaryFinder;

    public function __construct(BoundaryFinder $boundaryFinder){
        $this->boundaryFinder = $boundaryFinder;
    }

    public function findBoundaries(ModflowId $modelId): array
    {
        return $this->boundaryFinder->findByModelId($modelId);
    }

    public function getStressPeriods(ModflowId $modflowId, DateTime $start, DateTime $end): ?StressPeriods
    {
        $bcDates = $this->boundaryFinder->findStressPeriodDatesById($modflowId);

        if (is_null($bcDates)) {
            return null;
        }

        $bcDates[] = $start;
        $bcDates[] = $end;

        $dates = [];
        /** @var DateTime $bcDate */
        foreach ($bcDates as $bcDate){
            if ($bcDate->greaterOrEqualThen($start) && $bcDate->smallerOrEqualThen($end)){
                if (! in_array($bcDate, $dates)){
                    $dates[] = $bcDate;
                }
            }
        }

        $stressPeriods = StressPeriods::create();
        $totims = $this->calculateTotims($dates, TimeUnit::fromInt(TimeUnit::DAYS));
        for ($i=1; $i < count($totims); $i++){
            $perlen = ($totims[$i]->toInteger())-($totims[$i-1]->toInteger());
            $nstp = ($totims[$i]->toInteger())-($totims[$i-1]->toInteger());
            $tsmult = 1;
            $steady = false;
            $stressPeriods->addStressPeriod(StressPeriod::create(
                $totims[$i-1]->toInteger(),
                $perlen,
                $nstp,
                $tsmult,
                $steady
            ));
        }

        return $stressPeriods;
    }

    private function calculateTotims(array $bcDates, TimeUnit $timeUnit): array
    {
        $totims = [];
        $start = $bcDates[0];
        foreach ($bcDates as $bcDate){
            $totims[] = $this->calculateTotim($start, $bcDate, $timeUnit);
        }

        return $totims;
    }

    private function calculateTotim(DateTime $start, DateTime $dateTime, TimeUnit $timeUnit): TotalTime
    {
        $start = clone $start->toDateTime();
        $dateTime = clone $dateTime->toDateTime();
        $dateTime->modify('+1 day');
        $diff = $start->diff($dateTime);

        if ($timeUnit->toInt() === $timeUnit::SECONDS){
            return TotalTime::fromInt($dateTime->getTimestamp() - $start->getTimestamp());
        }

        if ($timeUnit->toInt() === $timeUnit::MINUTES){
            return TotalTime::fromInt((int)(($dateTime->getTimestamp() - $start->getTimestamp())/60));
        }

        if ($timeUnit->toInt() === $timeUnit::HOURS){
            return TotalTime::fromInt((int)(($dateTime->getTimestamp() - $start->getTimestamp())/60/60));
        }

        if ($timeUnit->toInt() === $timeUnit::DAYS){
            return TotalTime::fromInt((int)$diff->format("%a"));
        }

        throw InvalidTimeUnitException::withTimeUnitAndAvailableTimeUnits($timeUnit, $timeUnit->availableTimeUnits);
    }
}
