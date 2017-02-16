<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

interface ModflowModelCalculationList
{
    public function add(ModflowCalculationAggregate $calculation);

    public function get(ModflowId $calculationId);
}
