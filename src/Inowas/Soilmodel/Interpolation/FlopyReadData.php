<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Interpolation;

interface FlopyReadData
{
    public function readData(FlopyReadDataRequest $request);
}
