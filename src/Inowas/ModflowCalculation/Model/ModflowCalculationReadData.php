<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model;

interface ModflowCalculationReadData
{
    public function read(ModflowCalculationReadDataRequest $request);
}
