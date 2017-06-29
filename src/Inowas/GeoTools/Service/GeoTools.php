<?php

declare(strict_types=1);

namespace Inowas\GeoTools\Service;

use Doctrine\DBAL\Connection;
use Inowas\Common\Boundaries\Area;
use Inowas\Common\Boundaries\GridCellDateTimeValues;
use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\LineString;
use Inowas\Common\Geometry\LineStringWithObservationPoints;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Geometry\Srid;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\Distance;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Grid\LayerNumber;

class GeoTools
{
    /** @var Connection */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function calculateActiveCellsFromArea(Area $area, BoundingBox $boundingBox, GridSize $gridSize): ActiveCells
    {
        $geometry = Geometry::fromPolygon($area->geometry());
        $affectedLayers = AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger(0));

        return $this->calculateActiveCellsFromGeometryAndAffectedLayers($geometry, $affectedLayers, $boundingBox, $gridSize);
    }

    public function calculateActiveCellsFromBoundary(ModflowBoundary $boundary, BoundingBox $boundingBox, GridSize $gridSize): ActiveCells
    {
        $geometry = $boundary->geometry();
        $affectedLayers = $boundary->affectedLayers();
        return $this->calculateActiveCellsFromGeometryAndAffectedLayers($geometry, $affectedLayers, $boundingBox, $gridSize);
    }

    public function calculateActiveCellsFromGeometryAndAffectedLayers(Geometry $geometry, AffectedLayers $affectedLayers, BoundingBox $boundingBox, GridSize $gridSize): ActiveCells
    {
        /** @var \Polygon $boundingBoxPolygon */
        $boundaryGeometry = \geoPHP::load($geometry->toJson(), 'json')->geos();

        /** @var \Polygon $boundingBoxPolygon */
        $boundingBoxPolygon = \geoPHP::load($boundingBox->toGeoJson(), 'json')->geos();

        if (! $boundingBoxPolygon->intersects($boundaryGeometry)) {
            return ActiveCells::fromCells(array());
        }

        if ($geometry->value() instanceof Point){
            $gridCell = $this->getGridCellFromPoint($boundingBox, $gridSize, $geometry->value());
            $activeCells = [];
            $activeCells[$gridCell['row']][$gridCell['col']] = true;
            return ActiveCells::fromArrayGridSizeAndLayer($activeCells, $gridSize, $affectedLayers);
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
                $activeCells[$y][$x] = ($bb->intersects($boundaryGeometry) || $bb->crosses($boundaryGeometry));
            }
        }

        return ActiveCells::fromArrayGridSizeAndLayer($activeCells, $gridSize, $affectedLayers);
    }

    public function getBoundingBox(Geometry $geometry): BoundingBox
    {
        return $this->getBoundingBoxFromJson($geometry->toJson(), $geometry->srid());
    }

    public function getBoundingBoxFromPolygon(Polygon $polygon): BoundingBox
    {
        return $this->getBoundingBoxFromJson($polygon->toJson(), Srid::fromInt($polygon->getSrid()));
    }

    private function getBoundingBoxFromJson(string $json, Srid $srid): BoundingBox
    {
        $geometry = \geoPHP::load($json, 'json');
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
            $target->toInteger())
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

    public function getRelativeDistanceOfPointOnLineString(LineString $lineString, Point $point): float
    {
        $lineString = \geoPHP::load($lineString->toJson(),'json');
        $point = \geoPHP::load($point->toJson(),'json');

        $query = $this->connection
            ->prepare(sprintf(
                "SELECT ST_LineLocatePoint(ST_GeomFromText('%s')::geometry, ST_GeomFromText('%s')::geometry);",
                    $lineString->asText(),
                    $point->asText()
                )
            );

        $query->execute();
        $result = $query->fetch();
        return (float)$result['st_linelocatepoint'];
    }

    public function getClosestPointOnLineString(LineString $lineString, Point $point): Point
    {
        if ($lineString->getSrid() != $point->getSrid()){
            // @todo do something (convert, or throw an exception)
        }

        $srid = $point->getSrid();
        $point = \geoPHP::load($point->toJson(),'json');
        $point->setSRID($srid);

        $lineString = \geoPHP::load($lineString->toJson(),'json');
        $point->setSRID($srid);

        $result = $this->executeQuery(
            sprintf("SELECT ST_AsText(ST_ClosestPoint(line, pt)) AS cp_line_pt
                    FROM (SELECT '%s'::geometry as line, '%s'::geometry as pt) AS foo;",
                $lineString->asText(),
                $point->asText())
        );

        $point = \geoPHP::load($result['cp_line_pt'], 'wkt');
        $point->setSRID($srid);
        return new Point($point->getX(), $point->getY(), $srid);
    }

    /**
     * @param LineString $lineString
     * @param ObservationPoint[] $observationPoints
     * @return array
     */
    public function cutLinestringBetweenObservationPoints(LineString $lineString, array $observationPoints): array
    {
        foreach ($observationPoints as $observationPoint){
            if (! $observationPoint instanceof ObservationPoint){
                // @todo do something
                return null;
            }
        }

        $distances = [];
        foreach ($observationPoints as $observationPoint) {

            $point = $observationPoint->geometry()->value();
            if (! $point instanceof Point){
                // @todo do something
                return null;
            }

            $closestPoint = $this->getClosestPointOnLineString($lineString, $point);
            $distance = $this->getDistanceOfPointFromLineStringStartPoint($lineString, $closestPoint);
            $distances[] = $distance->inMeters();
        }

        /* Sort by distance  */
        array_multisort($distances, $observationPoints);

        /* Calculate Substrings */
        $substringsWithObservationPoints = array();
        foreach ($observationPoints as $key => $observationPoint) {
            if ($key === 0){continue;}
            $startPoint = $observationPoints[$key-1]->geometry()->value();
            $endPoint = $observationPoints[$key]->geometry()->value();
            $substringsWithObservationPoints[] = LineStringWithObservationPoints::create(
                $this->getSubstringOfLinestring($lineString, $startPoint, $endPoint),
                $observationPoints[$key-1],
                $observationPoints[$key]
            );
        }

        return $substringsWithObservationPoints;
    }

    public function interpolateGridCellDateTimeValuesFromLinestringAndObservationPoints(LineString $lineString, array $observationPoints, ActiveCells $activeCells, BoundingBox $boundingBox, GridSize $gridSize): array
    {
        // @todo Cut Linestring with boundingBox
        // Cut Linestring into sectors between ObservationPoints
        /** @var LineStringWithObservationPoints[] $sectors */
        $observationPoints = array_values($observationPoints);
        $sectors = $this->cutLinestringBetweenObservationPoints($lineString, $observationPoints);

        $gridCellDateTimeValues = array();
        foreach ($activeCells->cells() as $activeCell) {

            $layer = $activeCell[0];
            $row = $activeCell[1];
            $column = $activeCell[2];
            $activeCellCenter = $this->getPointFromGridCell($boundingBox, $gridSize, $row, $column);
            $closestPoint = $this->getClosestPointOnLineString($lineString, $activeCellCenter);
            $dateTimeValues = [];

            foreach ($sectors as $key => $sector){
                if ($this->pointIsOnLineString($sector->linestring(), $closestPoint)) {
                    $factor = $this->getRelativeDistanceOfPointOnLineString($sector->linestring(), $closestPoint);
                    $dateTimes = $sector->getDateTimes();

                    /** @var \DateTimeImmutable $dateTime */
                    foreach ($dateTimes as $dateTime){
                        $startValue = $sector->start()->findValueByDateTime($dateTime);
                        $endValue = $sector->end()->findValueByDateTime($dateTime);

                        $dateTimeClassName = get_class($startValue);

                        if (! $dateTimeClassName === get_class($endValue)){
                            // @todo throw something?!
                            continue;
                        }

                        $startArrayValues = $startValue->toArrayValues();
                        $endArrayValues = $endValue->toArrayValues();

                        $interpolatedDateTimeArrayValue = [$dateTime->format(DATE_ATOM)];
                        for ($i=1; $i<count($startArrayValues); $i++){
                            $interpolatedValue = $startArrayValues[$i] + (($endArrayValues[$i]-$startArrayValues[$i])*$factor);
                            $interpolatedDateTimeArrayValue[] = $interpolatedValue;
                        }

                        $dateTimeValues[] = $dateTimeClassName::fromArrayValues($interpolatedDateTimeArrayValue);
                    }

                    break;
                }
            }

            $gridCellDateTimeValues[] = GridCellDateTimeValues::fromParams($layer, $row, $column, $dateTimeValues);
        }

        return $gridCellDateTimeValues;
    }

    public function pointIsOnLineString(LineString $lineString, Point $point): bool
    {
        $lineString = \geoPHP::load($lineString->toJson(), 'json');
        $point = \geoPHP::load($point->toJson(), 'json');

        $geosLineString = $lineString->geos();
        $geosPoint = $point->geos();

        if ($geosLineString->contains($geosPoint) || $geosLineString->touches($geosPoint) || $geosLineString->intersects($geosPoint)){
            return true;
        }

        $result = $this->executeQuery(sprintf("SELECT ST_DWithin(
            ST_GeomFromText('%s')::geometry,
            ST_GeomFromText('%s')::geometry,
            0.0001
            )",
            $lineString->asText(),
            $point->asText()
        ));

        return $result['st_dwithin'];
    }

    protected function getDistanceOfTwoPointsOnALineStringInGeoPhpFormat(\LineString $lineString, \Point $p1, \Point $p2): float
    {
        $result = $this->executeQuery(
            sprintf("SELECT ST_Length(ST_LineSubstring(
                            line,
                            ST_LineLocatePoint(line, pta),
                            ST_LineLocatePoint(line, ptb))::geography)
                        FROM (
                          SELECT
                            ST_GeomFromText('%s')::geometry line, 
                            ST_GeomFromText('%s')::geometry pta, 
                            ST_GeomFromText('%s')::geometry ptb) AS data",
                $lineString->asText(), $p1->asText(), $p2->asText())
        );

        return (float)$result['st_length'];
    }

    protected function getSubstringOfLinestring(LineString $lineString, Point $start, Point $end): LineString
    {
        $srid =$lineString->getSrid();

        $start = \geoPHP::load($start->toJson(),'json');
        $start->setSRID($lineString->getSrid());

        $end = \geoPHP::load($end->toJson(),'json');
        $end->setSRID($lineString->getSrid());

        $lineString = \geoPHP::load($lineString->toJson(),'json');
        $end->setSRID($lineString->getSrid());

        $result = $this->executeQuery(
            sprintf("SELECT ST_AsText(ST_LineSubstring(
                line,
                ST_LineLocatePoint(line, pta),
                ST_LineLocatePoint(line, ptb))::geography)
            FROM (
              SELECT
                ST_GeomFromText('%s')::geometry line, 
                ST_GeomFromText('%s')::geometry pta, 
                ST_GeomFromText('%s')::geometry ptb) AS data",
            $lineString->asText(), $start->asText(), $end->asText()));

        $geometry = \geoPHP::load($result['st_astext'], 'wkt');
        $lineString =  new LineString($geometry->asArray());
        $lineString->setSrid($srid);
        return $lineString;
    }

    protected function updateBoundingBoxDistance(BoundingBox $bb): BoundingBox
    {
        $dx = \geoPHP::load(sprintf('LINESTRING(%f %f, %f %f)', $bb->xMin(), $bb->yMin(), $bb->xMax(), $bb->yMin(), 'wkt'))->greatCircleLength();
        $dy = \geoPHP::load(sprintf('LINESTRING(%f %f, %f %f)', $bb->xMin(), $bb->yMin(), $bb->xMin(), $bb->yMax(), 'wkt'))->greatCircleLength();

        return BoundingBox::fromCoordinates($bb->xMin(), $bb->xMax(), $bb->yMin(), $bb->yMax(), $bb->srid(), $dx, $dy);
    }

    protected function getGridCellFromPoint(BoundingBox $bb, GridSize $gz, Point $point)
    {

        // Todo !! Implement with tests
        // Transform Point to the same Coordinate System as BoundingBox
        $point = $this->projectPoint($point, Srid::fromInt($bb->srid()));

        $dx = ($bb->xMax() - $bb->xMin()) / $gz->nX();
        $dy = ($bb->yMax() - $bb->yMin()) / $gz->nY();

        $x = ceil(($point->getX() - $bb->xMin()) / $dx);
        $y = $gz->nY() - floor(($point->getY()-$bb->yMin()) / $dy);

        if ($y != 0){$y = $y-1;}
        if ($x != 0){$x = $x-1;}

        return array(
            "row" => $y,
            "col" => $x
        );
    }

    public function getPointFromGridCell(BoundingBox $bb, GridSize $gz, int $row, int $column): Point
    {
        $srid = $bb->srid();
        $dx = ($bb->xMax() - $bb->xMin()) / $gz->nX();
        $dy = ($bb->yMax() - $bb->yMin()) / $gz->nY();

        $x =  $bb->xMin() + ($column+0.5)*$dx;
        $y =  $bb->yMax() - ($row+0.5)*$dy;

        return new Point($x, $y, $srid);
    }

    protected function executeQuery(string $query): array
    {
        $query = $this->connection
            ->prepare($query);
        $query->execute();
        return $query->fetch();
    }
}
