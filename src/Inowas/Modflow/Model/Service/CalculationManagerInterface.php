<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Service;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\StressPeriods;

interface CalculationManagerInterface extends ModflowModelManagerInterface, SoilmodelManagerInterface
{
    public function findBoundaries(ModflowId $modflowModel): array;

    public function getStressPeriods(ModflowId $modflowId, DateTime $start, DateTime $end): ?StressPeriods;

    public function getBoundingBox(ModflowId $modflowId): ?BoundingBox;

    public function getGridSize(ModflowId $modflowId): ?GridSize;

    public function getAreaActiveCells(ModflowId $modflowId): ActiveCells;
}