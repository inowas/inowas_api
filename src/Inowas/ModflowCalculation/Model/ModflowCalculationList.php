<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model;

use Inowas\Common\Id\ModflowId;

interface ModflowCalculationList
{
    public function add(ModflowCalculationAggregate $calculation);

    public function get(ModflowId $calculationId);
}
