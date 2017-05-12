<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;

interface ModflowBoundary
{
    public static function create(BoundaryId $boundaryId);

    public function updateGeometry(Geometry $geometry);

    public function updateName(BoundaryName $boundaryName);

    public function boundaryId(): BoundaryId;

    public function name(): ?BoundaryName;

    public function geometry(): ?Geometry;

    public function observationPoints(): array;

    public function updateObservationPoint(ObservationPoint $op): void;

    public function getObservationPoint(ObservationPointId $id): ?ObservationPoint;

    public function type(): string;

    public function metadata(): array;

    public function dataToJson(): string;

    public function setActiveCells(ActiveCells $activeCells);

    public function activeCells(): ?ActiveCells;

    public function affectedLayers(): AffectedLayers;
}
