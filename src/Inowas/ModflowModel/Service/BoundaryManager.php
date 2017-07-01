<?php

namespace Inowas\ModflowModel\Service;

use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Boundaries\BoundaryType;
use Inowas\Common\Boundaries\ObservationPointName;
use Inowas\Common\Boundaries\RechargeBoundary;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\ObservationPointId;
use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Infrastructure\Projection\BoundaryList\BoundaryFinder;
use Inowas\ModflowModel\Infrastructure\Projection\ModelList\ModelFinder;

class BoundaryManager
{

    /** @var  ModelFinder */
    private $modelFinder;

    /** @var BoundaryFinder */
    private $boundaryFinder;

    /** @var  GeoTools */
    private $geoTools;

    public function __construct(ModelFinder $modelFinder, BoundaryFinder $boundaryFinder, GeoTools $geoTools)
    {
        $this->modelFinder = $modelFinder;
        $this->boundaryFinder = $boundaryFinder;
        $this->geoTools = $geoTools;
    }

    public function getTotalNumberOfModelBoundaries(ModflowId $modelId): int
    {
        return $this->boundaryFinder->getTotalNumberOfModelBoundaries($modelId);
    }

    public function getNumberOfModelBoundariesByType(ModflowId $modelId, BoundaryType $type): int
    {
        return $this->boundaryFinder->getNumberOfModelBoundariesByType($modelId, $type);
    }

    public function getAreaActiveCells(ModflowId $modelId): ActiveCells
    {
        $activeCells = $this->boundaryFinder->findAreaActiveCells($modelId);
        if ($activeCells instanceof ActiveCells) {
            return $activeCells;
        }

        $activeCells = $this->calculateAreaActiveCells($modelId);
        $this->boundaryFinder->updateAreaActiveCells($modelId, $activeCells);
        return $activeCells;
    }

    public function getBoundaryActiveCells(ModflowId $modelId, BoundaryId $boundaryId): ActiveCells
    {
        $activeCells = $this->boundaryFinder->findBoundaryActiveCells($modelId, $boundaryId);
        if ($activeCells instanceof ActiveCells) {
            return $activeCells;
        }

        $activeCells = $this->calculateBoundaryActiveCells($modelId, $boundaryId);
        $this->boundaryFinder->updateAreaActiveCells($modelId, $activeCells);
        return $activeCells;
    }

    public function findConstantHeadBoundaries(ModflowId $modelId): array
    {
        $boundaries = $this->boundaryFinder->findConstantHeadBoundaries($modelId);
        return $this->hydrateBoundaryActiveCells($modelId, $boundaries);
    }

    public function findGeneralHeadBoundaries(ModflowId $modelId): array
    {
        $boundaries = $this->boundaryFinder->findGeneralHeadBoundaries($modelId);
        return $this->hydrateBoundaryActiveCells($modelId, $boundaries);
    }

    public function findRechargeBoundaries(ModflowId $modelId): array
    {
        $boundaries = $this->boundaryFinder->findRechargeBoundaries($modelId);
        return $this->hydrateBoundaryActiveCells($modelId, $boundaries);
    }

    public function findRiverBoundaries(ModflowId $modelId): array
    {
        $boundaries = $this->boundaryFinder->findRiverBoundaries($modelId);
        return $this->hydrateBoundaryActiveCells($modelId, $boundaries);
    }

    public function findWellBoundaries(ModflowId $modelId): array
    {
        $boundaries = $this->boundaryFinder->findWellBoundaries($modelId);
        return $this->hydrateBoundaryActiveCells($modelId, $boundaries);
    }

    public function findBoundariesByModelId(ModflowId $modelId): array
    {
        return $this->boundaryFinder->findBoundariesByModelId($modelId);
    }

    public function getBoundaryDetails(ModflowId $modelId, BoundaryId $boundaryId): ?array
    {
        return $this->boundaryFinder->getBoundaryDetails($modelId, $boundaryId);
    }

    public function getBoundaryName(ModflowId $modelId, BoundaryId $boundaryId): ?BoundaryName
    {
        return $this->boundaryFinder->getBoundaryName($modelId, $boundaryId);
    }

    public function getBoundaryGeometry(ModflowId $modelId, BoundaryId $boundaryId): ?Geometry
    {
        return $this->boundaryFinder->getBoundaryGeometry($modelId, $boundaryId);
    }

    public function getBoundaryObservationPointDetails(ModflowId $modelId, BoundaryId $boundaryId, ObservationPointId $observationPointId): ?array
    {
        return $this->boundaryFinder->getBoundaryObservationPointDetails($modelId, $boundaryId, $observationPointId);
    }

    public function getBoundaryObservationPointName(ModflowId $modelId, BoundaryId $boundaryId, ObservationPointId $observationPointId): ?ObservationPointName
    {
        return $this->boundaryFinder->getBoundaryObservationPointName($modelId, $boundaryId, $observationPointId);
    }

    public function getBoundaryObservationPointGeometry(ModflowId $modelId, BoundaryId $boundaryId, ObservationPointId $observationPointId): ?Geometry
    {
        return $this->boundaryFinder->getBoundaryObservationPointGeometry($modelId, $boundaryId, $observationPointId);
    }

    public function getBoundaryObservationPointValues(ModflowId $modelId, BoundaryId $boundaryId, ObservationPointId $observationPointId): ?array
    {
        return $this->boundaryFinder->getBoundaryObservationPointValues($modelId, $boundaryId, $observationPointId);
    }

    public function getBoundaryType(ModflowId $modelId, BoundaryId $boundaryId): ?BoundaryType
    {
        return $this->boundaryFinder->getBoundaryType($modelId, $boundaryId);
    }

    public function getBoundaryTypeByBoundaryId(BoundaryId $boundaryId): ?BoundaryType
    {
        return $this->boundaryFinder->getBoundaryTypeByBoundaryId($boundaryId);
    }

    public function findStressPeriodDatesById(ModflowId $modelId): array
    {
        return $this->boundaryFinder->findStressPeriodDatesById($modelId);
    }

    public function getAffectedLayersByModelAndBoundary(ModflowId $modelId, BoundaryId $boundaryId): AffectedLayers
    {
        return $this->boundaryFinder->getAffectedLayersByModelAndBoundary($modelId, $boundaryId);
    }

    public function getBoundaryIdsByName(ModflowId $modflowId, BoundaryName $boundaryName): array
    {
        return $this->boundaryFinder->getBoundaryIdsByName($modflowId, $boundaryName);
    }

    public function getBoundaryIds(ModflowId $modflowId): array
    {
        return $this->boundaryFinder->getBoundaryIds($modflowId);
    }

    private function calculateAreaActiveCells(ModflowId $modelId): ActiveCells
    {
        $affectedLayers = AffectedLayers::fromArray([0]);
        $boundingBox = $this->modelFinder->getBoundingBoxByModflowModelId($modelId);
        $polygon = $this->modelFinder->getAreaPolygonByModflowModelId($modelId);
        $geometry = Geometry::fromPolygon($polygon);
        $gridSize = $this->modelFinder->getGridSizeByModflowModelId($modelId);
        return $this->geoTools->calculateActiveCellsFromGeometryAndAffectedLayers($geometry, $affectedLayers, $boundingBox, $gridSize);
    }

    private function calculateBoundaryActiveCells(ModflowId $modelId, BoundaryId $boundaryId): ActiveCells
    {
        $affectedLayers = $this->boundaryFinder->getAffectedLayersByModelAndBoundary($modelId, $boundaryId);
        $boundingBox = $this->modelFinder->getBoundingBoxByModflowModelId($modelId);
        $geometry = $this->boundaryFinder->getBoundaryGeometry($modelId, $boundaryId);
        $gridSize = $this->modelFinder->getGridSizeByModflowModelId($modelId);

        return $this->geoTools->calculateActiveCellsFromGeometryAndAffectedLayers($geometry, $affectedLayers, $boundingBox, $gridSize);
    }

    private function hydrateBoundaryActiveCells(ModflowId $modelId, array $boundaries): array
    {
        /** @var RechargeBoundary $boundary */
        foreach ($boundaries as $key => $boundary){
            $activeCells = $this->getBoundaryActiveCells($modelId, $boundary->boundaryId());
            $boundaries[$key] = $boundary->setActiveCells($activeCells);
        }

        return $boundaries;
    }
}
