<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Service;

use Inowas\Common\Boundaries\ConstantHeadBoundary;
use Inowas\Common\Boundaries\GeneralHeadBoundary;
use Inowas\Common\Boundaries\RechargeBoundary;
use Inowas\Common\Boundaries\RiverBoundary;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\ModflowCalculation\Service\StressPeriodDataGenerator;
use Inowas\ModflowModel\Model\Packages\ChdStressPeriodData;
use Inowas\ModflowModel\Model\Packages\GhbStressPeriodData;
use Inowas\ModflowModel\Model\Packages\RchStressPeriodData;
use Inowas\ModflowModel\Model\Packages\RivStressPeriodData;
use Inowas\ModflowModel\Model\Packages\WelStressPeriodData;
use Inowas\ModflowModel\Infrastructure\Projection\BoundaryList\BoundaryFinder;
use Inowas\ModflowModel\Infrastructure\Projection\ModelScenarioList\ModelScenarioFinder;

class ModflowModelManager implements ModflowModelManagerInterface
{

    /** @var  BoundaryFinder */
    protected $boundaryFinder;

    /** @var  ModelScenarioFinder */
    protected $modelFinder;

    /** @var  StressPeriodDataGenerator */
    protected $stressPeriodDataGenerator;

    public function __construct(BoundaryFinder $boundaryFinder, ModelScenarioFinder $modelFinder, StressPeriodDataGenerator $stressPeriodDataGenerator){
        $this->boundaryFinder = $boundaryFinder;
        $this->modelFinder = $modelFinder;
        $this->stressPeriodDataGenerator = $stressPeriodDataGenerator;
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

    public function generateChdStressPeriodData(ModflowId $modflowId, StressPeriods $stressPeriods): ChdStressPeriodData
    {
        $gridSize = $this->getGridSize($modflowId);
        $boundingBox = $this->getBoundingBox($modflowId);

        /** @var ConstantHeadBoundary[] $chdBoundaries */
        $chdBoundaries = $this->boundaryFinder->findChdBoundaries($modflowId);
        return $this->stressPeriodDataGenerator->fromConstantHeadBoundaries($chdBoundaries, $stressPeriods, $gridSize, $boundingBox);
    }

    public function generateGhbStressPeriodData(ModflowId $modflowId, StressPeriods $stressPeriods): GhbStressPeriodData
    {
        $gridSize = $this->getGridSize($modflowId);
        $boundingBox = $this->getBoundingBox($modflowId);

        /** @var GeneralHeadBoundary[] $ghbBoundaries */
        $ghbBoundaries = $this->boundaryFinder->findGhbBoundaries($modflowId);
        return $this->stressPeriodDataGenerator->fromGeneralHeadBoundaries($ghbBoundaries, $stressPeriods, $gridSize, $boundingBox);
    }

    public function generateRchStressPeriodData(ModflowId $modflowId, StressPeriods $stressPeriods): RchStressPeriodData
    {
        /** @var RechargeBoundary[] $rechargeBoundaries */
        $rechargeBoundaries = $this->boundaryFinder->findRecharge($modflowId);
        return $this->stressPeriodDataGenerator->fromRechargeBoundaries($rechargeBoundaries, $stressPeriods);
    }

    public function generateRivStressPeriodData(ModflowId $modflowId, StressPeriods $stressPeriods): RivStressPeriodData
    {
        $gridSize = $this->getGridSize($modflowId);
        $boundingBox = $this->getBoundingBox($modflowId);

        /** @var RiverBoundary[] $rivBoundaries */
        $rivBoundaries = $this->boundaryFinder->findRivers($modflowId);
        return $this->stressPeriodDataGenerator->fromRiverBoundaries($rivBoundaries, $stressPeriods, $gridSize, $boundingBox);
    }

    public function generateWelStressPeriodData(ModflowId $modflowId, StressPeriods $stressPeriods): WelStressPeriodData
    {
        /** @var WellBoundary[] $wellBoundaries */
        $wellBoundaries = $this->boundaryFinder->findWells($modflowId);
        return $this->stressPeriodDataGenerator->fromWellBoundaries($wellBoundaries, $stressPeriods);
    }
}
