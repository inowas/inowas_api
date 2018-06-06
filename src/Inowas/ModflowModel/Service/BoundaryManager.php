<?php

namespace Inowas\ModflowModel\Service;

use Inowas\Common\Boundaries\BoundaryList;
use Inowas\Common\Boundaries\BoundaryType;
use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\Name;
use Inowas\ModflowModel\Infrastructure\Projection\BoundaryList\BoundaryFinder;


class BoundaryManager
{
    /** @var BoundaryFinder */
    private $boundaryFinder;

    /**
     * BoundaryManager constructor.
     * @param BoundaryFinder $boundaryFinder
     */
    public function __construct(BoundaryFinder $boundaryFinder)
    {
        $this->boundaryFinder = $boundaryFinder;
    }

    /**
     * @param ModflowId $modelId
     * @return int
     * @throws \Inowas\ModflowModel\Model\Exception\SqlQueryException
     */
    public function getTotalNumberOfModelBoundaries(ModflowId $modelId): int
    {
        return $this->boundaryFinder->getTotalNumberOfModelBoundaries($modelId);
    }

    /**
     * @param ModflowId $modelId
     * @param BoundaryType $type
     * @return int
     * @throws \Inowas\ModflowModel\Model\Exception\SqlQueryException
     */
    public function getNumberOfModelBoundariesByType(ModflowId $modelId, BoundaryType $type): int
    {
        return $this->boundaryFinder->getNumberOfModelBoundariesByType($modelId, $type);
    }

    /**
     * @param ModflowId $modelId
     * @return array
     * @throws \Exception
     */
    public function findBoundaries(ModflowId $modelId): array
    {
        return $this->boundaryFinder->findBoundaries($modelId);
    }

    /**
     * @param ModflowId $modelId
     * @return array
     * @throws \Inowas\Common\Exception\InvalidTypeException
     * @throws \Exception
     */
    public function findConstantHeadBoundaries(ModflowId $modelId): array
    {
        return $this->boundaryFinder->findConstantHeadBoundaries($modelId);
    }

    /**
     * @param ModflowId $modelId
     * @return array
     * @throws \Inowas\Common\Exception\InvalidTypeException
     * @throws \Exception
     */
    public function findGeneralHeadBoundaries(ModflowId $modelId): array
    {
        return $this->boundaryFinder->findGeneralHeadBoundaries($modelId);
    }

    /**
     * @param ModflowId $modelId
     * @return array
     * @throws \Inowas\Common\Exception\InvalidTypeException
     * @throws \Exception
     */
    public function findRechargeBoundaries(ModflowId $modelId): array
    {
        return $this->boundaryFinder->findRechargeBoundaries($modelId);
    }

    /**
     * @param ModflowId $modelId
     * @return array
     * @throws \Inowas\Common\Exception\InvalidTypeException
     * @throws \Exception
     */
    public function findRiverBoundaries(ModflowId $modelId): array
    {
        return $this->boundaryFinder->findRiverBoundaries($modelId);
    }

    /**
     * @param ModflowId $modelId
     * @return array
     * @throws \Inowas\Common\Exception\InvalidTypeException
     * @throws \Exception
     */
    public function findWellBoundaries(ModflowId $modelId): array
    {
        return $this->boundaryFinder->findWellBoundaries($modelId);
    }

    /**
     * @param ModflowId $modelId
     * @return array
     * @throws \Inowas\Common\Exception\InvalidTypeException
     * @throws \Exception
     */
    public function findHeadObservationWells(ModflowId $modelId): array
    {
        return $this->boundaryFinder->findHeadObservationWells($modelId);
    }

    /**
     * @param ModflowId $modelId
     * @return array
     */
    public function findBoundariesByModelId(ModflowId $modelId): array
    {
        return $this->boundaryFinder->findBoundariesByModelId($modelId);
    }

    /**
     * @param ModflowId $modelId
     * @param BoundaryId $boundaryId
     * @return ModflowBoundary|null
     * @throws \Exception
     */
    public function getBoundary(ModflowId $modelId, BoundaryId $boundaryId): ?ModflowBoundary
    {
        return $this->boundaryFinder->getBoundary($modelId, $boundaryId);
    }

    /**
     * @param ModflowId $modelId
     * @param BoundaryId $boundaryId
     * @return Name|null
     */
    public function getBoundaryName(ModflowId $modelId, BoundaryId $boundaryId): ?Name
    {
        return $this->boundaryFinder->getBoundaryName($modelId, $boundaryId);
    }

    /**
     * @param ModflowId $modelId
     * @param BoundaryId $boundaryId
     * @return Geometry|null
     */
    public function getBoundaryGeometry(ModflowId $modelId, BoundaryId $boundaryId): ?Geometry
    {
        return $this->boundaryFinder->getBoundaryGeometry($modelId, $boundaryId);
    }

    /**
     * @param ModflowId $modelId
     * @param BoundaryId $boundaryId
     * @return BoundaryType|null
     * @throws \Inowas\Common\Exception\InvalidTypeException
     */
    public function getBoundaryType(ModflowId $modelId, BoundaryId $boundaryId): ?BoundaryType
    {
        return $this->boundaryFinder->getBoundaryType($modelId, $boundaryId);
    }

    /**
     * @param ModflowId $modelId
     * @return array
     * @throws \Inowas\ModflowModel\Model\Exception\SqlQueryException
     * @throws \Exception
     */
    public function findStressPeriodDatesById(ModflowId $modelId): array
    {
        return $this->boundaryFinder->findStressPeriodDatesById($modelId);
    }

    /**
     * @param ModflowId $modelId
     * @param BoundaryId $boundaryId
     * @return AffectedLayers
     */
    public function getAffectedLayersByModelAndBoundary(ModflowId $modelId, BoundaryId $boundaryId): AffectedLayers
    {
        return $this->boundaryFinder->getAffectedLayersByModelAndBoundary($modelId, $boundaryId);
    }

    /**
     * @param ModflowId $modelId
     * @return BoundaryList
     * @throws \Inowas\Common\Exception\InvalidTypeException
     */
    public function getBoundaryList(ModflowId $modelId): BoundaryList
    {
        return $this->boundaryFinder->getBoundaryList($modelId);
    }
}
