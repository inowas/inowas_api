<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Service;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\Packages\WelStressPeriodData;

interface ModflowModelManagerInterface
{
    public function findBoundaries(ModflowId $modflowModel): array;

    public function countModelBoundaries(ModflowId $modflowId, string $type): int;

    public function findWelStressPeriodData(ModflowId $modflowId, StressPeriods $stressPeriods, DateTime $start, TimeUnit $timeUnit): WelStressPeriodData;

    public function calculateStressPeriods(ModflowId $modflowId, DateTime $start, DateTime $end): ?StressPeriods;

    public function getBoundingBox(ModflowId $modflowId): ?BoundingBox;

    public function getGridSize(ModflowId $modflowId): ?GridSize;

    public function getAreaActiveCells(ModflowId $modflowId): ActiveCells;
}
