<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Id\BoundaryId;

class AreaBoundary extends AbstractBoundary
{
    public static function create(BoundaryId $boundaryId): AreaBoundary
    {
        return new self($boundaryId);
    }

    public function setName(BoundaryName $name): AreaBoundary
    {
        return new self($this->boundaryId, $name, $this->geometry);
    }

    public function setGeometry(Geometry $geometry): AreaBoundary
    {
        return new self($this->boundaryId, $this->name, $geometry);
    }

    public function type(): string
    {
        return 'area';
    }

    public function metadata(): array
    {
        return [];
    }

    public function dataToJson(): string
    {
        return json_encode([]);
    }
}
