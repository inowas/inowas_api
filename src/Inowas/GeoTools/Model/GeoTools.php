<?php

namespace Inowas\GeoTools\Model;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\LineString;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Geometry\Srid;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\Distance;
use Inowas\Common\Grid\GridSize;

class GeoTools
{

    /** @var Connection */
    protected $connection;

    public function __construct(EntityManager $em)
    {
        $this->connection = $em->getConnection();
    }

    public function calculateActiveCells(ModflowBoundary $boundary, BoundingBox $boundingBox, GridSize $gridSize): ActiveCells
    {
        if ($boundary->geometry()->value() instanceof Point) {
            return $this->getActiveCellsFromPoint($boundingBox, $gridSize, $boundary->geometry()->value());
        }

        /** @var \Polygon $boundingBoxPolygon */
        $boundary = \geoPHP::load($boundary->geometry()->toJson(), 'json')->geos();

        /** @var \Polygon $boundingBoxPolygon */
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
                /** @var \Polygon $bb */
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

        $bb = BoundingBox::fromCoordinates($bb['minx'], $bb['maxx'], $bb['miny'], $bb['maxy'], $srid->toInteger(), 0,0);
        return $this->updateBoundingBoxDistance($bb);
    }

    public function distanceInMeters(Point $pointA, Point $pointB): Distance
    {
        $distance = \geoPHP::load(sprintf('LINESTRING(%f %f, %f %f)', $pointA->getX(), $pointA->getY(), $pointB->getX(), $pointB->getY(), 'wkt'))->greatCircleLength();
        return Distance::fromMeters($distance);
    }

    public function projectBoundingBox(BoundingBox $boundingBox, Srid $target): BoundingBox
    {
        $topLeft = new Point($boundingBox->xMin(), $boundingBox->yMax(), $boundingBox->srid());
        $topLeft = $this->projectPoint($topLeft, $target);

        $bottomRight = new Point($boundingBox->xMax(), $boundingBox->yMin(), $boundingBox->srid());
        $bottomRight = $this->projectPoint($bottomRight, $target);

        $bb = BoundingBox::fromCoordinates(
            $topLeft->getX(),
            $bottomRight->getX(),
            $topLeft->getY(),
            $bottomRight->getY(),
            $target->toInteger(),
            0,
            0)
        ;

        return $this->updateBoundingBoxDistance($bb);
    }

    public function projectPoint(Point $point, Srid $target): Point
    {
        if ($point->getSrid() == $target->toInteger()){
            return $point;
        }

        $query = $this->connection
            ->prepare(sprintf('SELECT ST_AsGeoJson(ST_TRANSFORM(ST_SetSRID(ST_Point(%s, %s), %s), %s))',
                $point->getX(),
                $point->getY(),
                $point->getSrid(),
                $target->toInteger()
            ))
        ;

        $query->execute();
        $result = json_decode($query->fetchAll()[0]['st_asgeojson']);

        return new Point($result->coordinates[0], $result->coordinates[1], $target->toInteger());
    }

    public function getDistanceOfTwoPointsOnALineString(LineString $lineString, Point $p1, Point $p2): Distance
    {
        $lineString = \geoPHP::load($lineString->toJson(),'json');
        $p1 = \geoPHP::load($p1->toJson(),'json');
        $p2 = \geoPHP::load($p2->toJson(),'json');

        $distanceInMeters = $this->getDistanceOfTwoPointsOnALineStringInGeoPhpFormat($lineString, $p1, $p2);
        return Distance::fromMeters($distanceInMeters);
    }

    public function getDistanceOfPointFromLineStringStartPoint(LineString $lineString, Point $p2): Distance
    {
        $lineString = \geoPHP::load($lineString->toJson(),'json');
        $p1 = $lineString->startPoint();
        $p2 = \geoPHP::load($p2->toJson(),'json');

        $distanceInMeters = $this->getDistanceOfTwoPointsOnALineStringInGeoPhpFormat($lineString, $p1, $p2);
        return Distance::fromMeters($distanceInMeters);
    }

    protected function getDistanceOfTwoPointsOnALineStringInGeoPhpFormat(\LineString $lineString, \Point $p1, \Point $p2): float
    {
        $query = $this->connection
            ->prepare(sprintf("SELECT ST_Length(ST_LineSubstring(
                            line,
                            ST_LineLocatePoint(line, pta),
                            ST_LineLocatePoint(line, ptb))::geography)
                        FROM (
                          SELECT
                            ST_GeomFromText('%s')::geometry line, 
                            ST_GeomFromText('%s')::geometry pta, 
                            ST_GeomFromText('%s')::geometry ptb) AS data",
                $lineString->asText(), $p1->asText(), $p2->asText()));

        $query->execute();
        $result = $query->fetch();
        return $result['st_length'];
    }

    protected function updateBoundingBoxDistance(BoundingBox $bb): BoundingBox
    {
        $dx = \geoPHP::load(sprintf('LINESTRING(%f %f, %f %f)', $bb->xMin(), $bb->yMin(), $bb->xMax(), $bb->yMin(), 'wkt'))->greatCircleLength();
        $dy = \geoPHP::load(sprintf('LINESTRING(%f %f, %f %f)', $bb->xMin(), $bb->yMin(), $bb->xMin(), $bb->yMax(), 'wkt'))->greatCircleLength();

        return BoundingBox::fromCoordinates($bb->xMin(), $bb->xMax(), $bb->yMin(), $bb->yMax(), $bb->srid(), $dx, $dy);
    }

    protected function getActiveCellsFromPoint(BoundingBox $bb, GridSize $gz, Point $point){
        $result = $this->getGridCellFromPoint($bb, $gz, $point);
        $cells = array();
        $cells[$result['row']][$result['col']]=true;
        return ActiveCells::fromArrayAndGridSize($cells, $gz);
    }

    protected function getGridCellFromPoint(BoundingBox $bb, GridSize $gz, Point $point)
    {
        // Transform Point to the same Coordinate System as BoundingBox
        $point = $this->projectPoint($point, Srid::fromInt($bb->srid()));
        // Check if point is inside of BoundingBox
        if (!($point->getX() >= $bb->xMin()
            && $point->getX() <= $bb->xMax()
            && $point->getY() >= $bb->yMin()
            && $point->getY() <= $bb->yMax())
        ) {
            return null;
        }
        $dx = ($bb->xMax() - $bb->xMin()) / $gz->nX();
        $dy = ($bb->yMax() - $bb->yMin()) / $gz->nY();
        $col = (int)(floor(($point->getX() - $bb->xMin()) / $dx));
        $row = (int)($gz->nY()-ceil(($point->getY() - $bb->yMin()) / $dy));
        return array(
            "row" => $row,
            "col" => $col
        );
    }
}
