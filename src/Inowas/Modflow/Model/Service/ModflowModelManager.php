<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Service;

use Inowas\Common\Boundaries\ConstantHeadBoundary;
use Inowas\Common\Boundaries\ConstantHeadDateTimeValue;
use Inowas\Common\Boundaries\GeneralHeadBoundary;
use Inowas\Common\Boundaries\RechargeBoundary;
use Inowas\Common\Boundaries\RiverBoundary;
use Inowas\Common\Boundaries\RiverDateTimeValue;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\Rech;
use Inowas\Common\Modflow\StressPeriod;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\Exception\InvalidTimeUnitException;
use Inowas\Modflow\Model\Packages\ChdStressPeriodData;
use Inowas\Modflow\Model\Packages\ChdStressPeriodGridCellValue;
use Inowas\Modflow\Model\Packages\GhbStressPeriodData;
use Inowas\Modflow\Model\Packages\RchStressPeriodData;
use Inowas\Modflow\Model\Packages\RchStressPeriodValue;
use Inowas\Modflow\Model\Packages\RivStressPeriodData;
use Inowas\Modflow\Model\Packages\RivStressPeriodGridCellValue;
use Inowas\Modflow\Model\Packages\StressPeriodDataGenerator;
use Inowas\Modflow\Model\Packages\WelStressPeriodData;
use Inowas\Modflow\Model\Packages\WelStressPeriodGridCellValue;
use Inowas\Modflow\Projection\BoundaryList\BoundaryFinder;
use Inowas\Modflow\Projection\ModelScenarioList\ModelScenarioFinder;

class ModflowModelManager implements ModflowModelManagerInterface
{

    /** @var  BoundaryFinder */
    protected $boundaryFinder;

    /** @var  ModelScenarioFinder */
    protected $modelFinder;

    public function __construct(BoundaryFinder $boundaryFinder, ModelScenarioFinder $modelFinder){
        $this->boundaryFinder = $boundaryFinder;
        $this->modelFinder = $modelFinder;
    }

    public function findBoundaries(ModflowId $modelId): array
    {
        return $this->boundaryFinder->findByModelId($modelId);
    }

    public function calculateStressPeriods(ModflowId $modflowId, DateTime $start, DateTime $end, TimeUnit $timeUnit): StressPeriods
    {
        /** @var DateTime[] $bcDates */
        $dates = $this->boundaryFinder->findStressPeriodDatesById($modflowId);
        $stressPeriods = StressPeriods::createFromDates($dates, $start, $end, $timeUnit);
        return $stressPeriods;
    }

    public function getAreaActiveCells(ModflowId $modflowId): ActiveCells
    {
        return $this->boundaryFinder->findAreaActiveCells($modflowId);
    }

    public function getBoundingBox(ModflowId $modflowId): BoundingBox
    {
        return $this->modelFinder->findBoundingBoxByModelId($modflowId);
    }

    public function getGridSize(ModflowId $modflowId): GridSize
    {
        return $this->modelFinder->findGridSizeByModelId($modflowId);
    }

    public function countModelBoundaries(ModflowId $modflowId, string $type): int
    {
        return $this->boundaryFinder->countModelBoundaries($modflowId, $type);
    }

    public function findChdStressPeriodData(ModflowId $modflowId, StressPeriods $stressPeriods, DateTime $start, TimeUnit $timeUnit): ChdStressPeriodData
    {
        $chdSpd = ChdStressPeriodData::create();

        /** @var ConstantHeadBoundary[] $chdBoundaries */
        $chdBoundaries = $this->boundaryFinder->findChdBoundaries($modflowId);

        /** @var StressPeriod $stressperiod */
        foreach ($stressPeriods->stressperiods() as $stressperiod) {
            $totim = TotalTime::fromInt($stressperiod->totimStart());
            $sp = $stressPeriods->spNumberFromTotim($totim);

            foreach ($chdBoundaries as $chdBoundary) {
                $cells = $chdBoundary->activeCells()->cells();
                if (count($cells)>0) {
                    foreach ($cells as $cell){
                        $dateTimeValue = $chdBoundary->findValueByDateTime($this->calculateDateTimeFromTotim($start, $totim, $timeUnit));
                        if ($dateTimeValue instanceof ConstantHeadDateTimeValue){
                            $chdSpd->addGridCellValue(ChdStressPeriodGridCellValue::fromParams($sp, $cell[0], $cell[1], $cell[2], $dateTimeValue->shead(), $dateTimeValue->ehead()));
                        }
                    }
                }
            }
        }

        return $chdSpd;
    }

    public function findGhbStressPeriodData(ModflowId $modflowId, StressPeriods $stressPeriods): GhbStressPeriodData
    {
        $gridSize = $this->getGridSize($modflowId);
        $boundingBox = $this->getBoundingBox($modflowId);

        /** @var GeneralHeadBoundary[] $ghbBoundaries */
        $ghbBoundaries = $this->boundaryFinder->findGhbBoundaries($modflowId);
        $stressPeriodDataGenerator = new StressPeriodDataGenerator();
        return $stressPeriodDataGenerator->fromGeneralHeadBoundaries($ghbBoundaries, $stressPeriods, $gridSize, $boundingBox);
    }

    public function findRchStressPeriodData(ModflowId $modflowId, StressPeriods $stressPeriods, DateTime $start, TimeUnit $timeUnit): RchStressPeriodData
    {
        $rchSpd = RchStressPeriodData::create();

        /** @var RechargeBoundary[] $recharges */
        $recharges = $this->boundaryFinder->findRecharge($modflowId);

        /** @var StressPeriod $stressperiod */
        foreach ($stressPeriods->stressperiods() as $stressperiod) {
            $totim = TotalTime::fromInt($stressperiod->totimStart());
            $sp = $stressPeriods->spNumberFromTotim($totim);

            $rech = 0;
            foreach ($recharges as $recharge) {
                $cells = $recharge->activeCells()->fullArray();
                $rechargeValue = $recharge->findValueByDateTime($this->calculateDateTimeFromTotim($start, $totim, $timeUnit));

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
            }

            $rchSpd->addStressPeriodValue(RchStressPeriodValue::fromParams($sp, Rech::fromValue($rech)));
        }

        return $rchSpd;
    }

    public function findRivStressPeriodData(ModflowId $modflowId, StressPeriods $stressPeriods, DateTime $start, TimeUnit $timeUnit): RivStressPeriodData
    {
        $rivSpd = RivStressPeriodData::create();

        /** @var RiverBoundary[] $rivers */
        $rivers = $this->boundaryFinder->findRivers($modflowId);

        /** @var StressPeriod $stressperiod */
        foreach ($stressPeriods->stressperiods() as $stressperiod) {
            $totim = TotalTime::fromInt($stressperiod->totimStart());
            $sp = $stressPeriods->spNumberFromTotim($totim);

            foreach ($rivers as $river) {
                $cells = $river->activeCells()->cells();
                if (count($cells)>0) {
                    foreach ($cells as $cell){
                        $dateTimeValue = $river->findValueByDateTime($this->calculateDateTimeFromTotim($start, $totim, $timeUnit));
                        if ($dateTimeValue instanceof RiverDateTimeValue){
                            $rivSpd->addGridCellValue(RivStressPeriodGridCellValue::fromParams($sp, $cell[0], $cell[1], $cell[2], $dateTimeValue->stage(), $dateTimeValue->cond(), $dateTimeValue->rbot()));
                        }
                    }
                }
            }
        }

        return $rivSpd;
    }

    public function findWelStressPeriodData(ModflowId $modflowId, StressPeriods $stressPeriods, DateTime $start, TimeUnit $timeUnit): WelStressPeriodData
    {
        $wspd = WelStressPeriodData::create();

        /** @var WellBoundary[] $wells */
        $wells = $this->boundaryFinder->findWells($modflowId);

        /** @var StressPeriod $stressperiod */
        foreach ($stressPeriods->stressperiods() as $stressperiod) {
            $totim = TotalTime::fromInt($stressperiod->totimStart());
            $sp = $stressPeriods->spNumberFromTotim($totim);

            foreach ($wells as $well) {
                $cells = $well->activeCells()->cells();
                if (count($cells)>0) {
                    $cell = $cells[0];
                    $pumpingRate = $well->findValueByDateTime($this->calculateDateTimeFromTotim($start, $totim, $timeUnit));
                    $wspd->addGridCellValue(WelStressPeriodGridCellValue::fromParams($sp, $cell[0], $cell[1], $cell[2], $pumpingRate->pumpingRate()));
                }
            }
        }

        return $wspd;
    }

    private function calculateDateTimeFromTotim(DateTime $start, TotalTime $totalTime, TimeUnit $timeUnit): \DateTimeImmutable
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
