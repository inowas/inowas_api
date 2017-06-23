<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model;

use Inowas\ModflowModel\Model\AMQP\CalculationRequest;

interface ModflowCalculation
{
    public function calculate(CalculationRequest $configuration);
}
