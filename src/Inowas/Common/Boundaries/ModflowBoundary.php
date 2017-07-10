<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;

interface ModflowBoundary
{
    /** @noinspection ReturnTypeCanBeDeclaredInspection */
    /** @noinspection MoreThanThreeArgumentsInspection
     * @param BoundaryId $boundaryId
     * @param BoundaryName $name
     * @param Geometry $geometry
     * @param AffectedLayers $affectedLayers
     * @param BoundaryMetadata $metadata
     * @return ModflowBoundary
     */
    public static function createWithParams(
        BoundaryId $boundaryId,
        BoundaryName $name,
        Geometry $geometry,
        AffectedLayers $affectedLayers,
        BoundaryMetadata $metadata
    );

    public function updateGeometry(Geometry $geometry);

    public function updateName(BoundaryName $boundaryName);

    public function boundaryId(): BoundaryId;

    public function name(): BoundaryName;

    public function geometry(): Geometry;

    public function observationPoints(): ObservationPointCollection;

    public function addObservationPoint(ObservationPoint $op): ModflowBoundary;

    public function getObservationPoint(ObservationPointId $id): ObservationPoint;

    public function updateObservationPoint(ObservationPoint $op): ModflowBoundary;

    public function type(): BoundaryType;

    public function cardinality(): Cardinality;

    public function affectedLayers(): AffectedLayers;

    public function metadata(): BoundaryMetadata;
}
