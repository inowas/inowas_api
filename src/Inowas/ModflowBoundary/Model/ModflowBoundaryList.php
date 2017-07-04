<?php

declare(strict_types=1);

namespace Inowas\ModflowBoundary\Model;

use Inowas\Common\Id\BoundaryId;
use Inowas\ModflowBoundary\Model\ModflowBoundaryAggregate;

interface ModflowBoundaryList
{
    public function add(ModflowBoundaryAggregate $boundary);

    public function get(BoundaryId $boundaryId);
}
