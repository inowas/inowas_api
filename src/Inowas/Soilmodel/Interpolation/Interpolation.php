<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Interpolation;

interface Interpolation
{
    public function interpolate(InterpolationConfiguration $configuration): InterpolationResult;
}
