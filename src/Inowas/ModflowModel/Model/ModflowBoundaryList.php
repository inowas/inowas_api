<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model;

use Inowas\Common\Id\BoundaryId;

interface ModflowBoundaryList
{
    public function add(ModflowBoundaryAggregate $boundary);

    public function get(BoundaryId $boundaryId);
}
