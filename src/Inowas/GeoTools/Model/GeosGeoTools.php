<?php

namespace Inowas\GeoTools\Model;

use Inowas\Common\Boundaries\AbstractBoundary;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Geometry\Srid;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\Distance;
use Inowas\Common\Grid\GridSize;

class GeosGeoTools implements GeoTools
{

    public function calculateActiveCells(AbstractBoundary $boundary, BoundingBox $boundingBox, GridSize $gridSize): ActiveCells
    {
        $boundary = \geoPHP::load($boundary->geometry()->toJson(), 'json')->geos();
        $boundingBoxPolygon = \geoPHP::load($boundingBox->toGeoJson(), 'json')->geos();

        if (! $boundingBoxPolygon->intersects($boundary)) {
            return ActiveCells::fromCells(array());
        }

        $dX = ($boundingBox->xMax()-$boundingBox->xMin())/$gridSize->nX();
        $dY = ($boundingBox->yMax()-$boundingBox->yMin())/$gridSize->nY();
        $nx = $gridSize->nX();
        $ny = $gridSize->nY();

        $activeCells = [];
        for ($y = 0; $y<$ny; $y++){
            $activeCells[$y] = [];
            for ($x = 0; $x<$nx; $x++){
                $bb = \geoPHP::load(sprintf('LINESTRING(%f %f, %f %f)', $boundingBox->xMin()+(($x)*$dX), $boundingBox->yMax()-(($y)*$dY), $boundingBox->xMin()+(($x+1)*$dX), $boundingBox->yMax()-(($y+1)*$dY)), 'wkt')->envelope()->geos();
                $activeCells[$y][$x] = ($bb->intersects($boundary) || $bb->crosses($boundary));
            }
        }

        return ActiveCells::fromArrayAndGridSize($activeCells, $gridSize);
    }

    public function getBoundingBox(Geometry $geometry): BoundingBox
    {
        $srid = $geometry->srid();
        $geometry = \geoPHP::load($geometry->toJson(), 'json');
        $geometry->setSRID($srid->toInteger());
        $bb = $geometry->getBBox();

        $dx = \geoPHP::load(sprintf('LINESTRING(%f %f, %f %f)', $bb['minx'], $bb['miny'], $bb['maxx'], $bb['miny'], 'wkt'))->greatCircleLength();
        $dy = \geoPHP::load(sprintf('LINESTRING(%f %f, %f %f)', $bb['minx'], $bb['miny'], $bb['minx'], $bb['maxy'], 'wkt'))->greatCircleLength();

        return BoundingBox::fromCoordinates($bb['minx'], $bb['maxx'], $bb['miny'], $bb['maxy'], $srid->toInteger(), $dx, $dy);
    }

    public function distanceInMeters(Point $pointA, Point $pointB): Distance
    {
        $distance = \geoPHP::load(sprintf('LINESTRING(%f %f, %f %f)', $pointA->getX(), $pointA->getY(), $pointB->getX(), $pointB->getY(), 'wkt'))->greatCircleLength();
        return Distance::fromMeters($distance);
    }

    /*
    public function projectBoundingBox(BoundingBox $boundingBox, Srid $target): BoundingBox
    {
        return BoundingBox::fromArray()
    }
    */

    /*
    public function projectGeometry(Geometry $geometry, Srid $target): Geometry
    {
        $srid = $geometry->srid();
        $geometry = \geoPHP::load($geometry->toJson(), 'json');
        $geometry->setSRID($srid->toInteger());
        $point = $geometry->getPoints()[0];

        $geometry = $geometry->geos();
        var_dump($geometry->project($point));
        die();
    }
    */
}
