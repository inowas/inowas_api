<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Interpolation;

interface FlopyCalculation
{
    public function calculate(FlopyConfiguration $configuration);
}
