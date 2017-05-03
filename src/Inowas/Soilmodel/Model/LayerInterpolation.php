<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;

use Inowas\Soilmodel\Model\LayerInterpolationConfiguration;
use Inowas\Soilmodel\Model\LayerInterpolationResult;

interface LayerInterpolation
{
    public function interpolate(LayerInterpolationConfiguration $configuration): LayerInterpolationResult;
}
