<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model;

interface ModflowCalculation
{
    public function calculate(ModflowCalculationConfigurationRequest $configuration);
}
