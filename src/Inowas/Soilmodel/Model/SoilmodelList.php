<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;


interface SoilmodelList
{
    public function add(SoilmodelAggregate $soilmodel);

    public function get(SoilmodelId $soilmodelId);
}
