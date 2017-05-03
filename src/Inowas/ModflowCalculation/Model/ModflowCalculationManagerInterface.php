<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\ModflowModel\Service\ModflowModelManagerInterface;
use Inowas\Soilmodel\Model\SoilmodelManagerInterface;

interface ModflowCalculationManagerInterface extends ModflowModelManagerInterface, SoilmodelManagerInterface
{
    public function findBoundaries(ModflowId $modflowModel): array;

    public function calculateStressPeriods(ModflowId $modflowId, DateTime $start, DateTime $end, TimeUnit $timeUnit): ?StressPeriods;

    public function getBoundingBox(ModflowId $modflowId): ?BoundingBox;

    public function getGridSize(ModflowId $modflowId): ?GridSize;

    public function getAreaActiveCells(ModflowId $modflowId): ActiveCells;
}
