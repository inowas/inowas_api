<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Interpolation;

interface FlopyReadData
{
    public function read(FlopyReadDataRequest $request);
}
