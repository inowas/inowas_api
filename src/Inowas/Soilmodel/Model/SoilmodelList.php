<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;


use Inowas\Common\Soilmodel\SoilmodelId;

interface SoilmodelList
{
    public function add(SoilmodelAggregate $soilmodel);

    public function get(SoilmodelId $soilmodelId);
}
