<?php

declare(strict_types=1);

namespace Inowas\GeoToolsBundle\Model;

use Inowas\Common\Boundaries\AbstractBoundary;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Geometry\Srid;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\Distance;
use Inowas\Common\Grid\GridSize;

interface GeoTools
{
    public function calculateActiveCells(AbstractBoundary $boundary, BoundingBox $boundingBox, GridSize $gridSize): ActiveCells;

    public function getBoundingBox(Geometry $geometry): BoundingBox;

    #public function projectBoundingBox(BoundingBox $geometry, Srid $target): BoundingBox;

    #public function projectGeometry(Geometry $geometry, Srid $target): Geometry;

    #public function distanceInMeters(Point $pointA, Point $pointB): Distance;
}
