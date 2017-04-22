<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Id\BoundaryId;

interface ModflowBoundary
{
    public static function create(BoundaryId $boundaryId);

    public function updateGeometry(Geometry $geometry);

    public function boundaryId(): BoundaryId;

    public function name(): ?BoundaryName;

    public function geometry(): ?Geometry;

    public function observationPoints(): array;

    public function type(): string;

    public function metadata(): array;

    public function dataToJson(): string;

    public function setActiveCells(ActiveCells $activeCells);
}
