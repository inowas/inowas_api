<?php

namespace Inowas\Modflow\Model\Service;


use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\StressPeriods;

interface ModflowModelManagerInterface
{
    public function findBoundaries(ModflowId $modflowModel): array;

    public function getStressPeriods(ModflowId $modflowId, DateTime $start, DateTime $end): ?StressPeriods;
}
