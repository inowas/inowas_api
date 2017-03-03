<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

use Inowas\Common\Id\ModflowId;

interface ModflowModelCalculationList
{
    public function add(ModflowCalculationAggregate $calculation);

    public function get(ModflowId $calculationId);
}
