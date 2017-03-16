<?php

namespace Inowas\GeoToolsBundle\Service;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Inowas\Common\Boundaries\AreaBoundary;
use Inowas\ModflowBundle\Model\ActiveCells;
use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\BoundingBox;
use Inowas\ModflowBundle\Model\GridSize;
use Inowas\ModflowBundle\Model\ModelObject;

ini_set('memory_limit', '500M');

class GeoTools
{

    /** @var  EntityManager */
    protected $entityManager;

    /** @var  Connection */
    protected $connection;

    /**
     * GeoTools constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->connection = $entityManager->getConnection();
    }

    public function getActiveCellsFromArea(AreaBoundary $area, \Inowas\Common\Grid\BoundingBox $boundingBox, \Inowas\Common\Grid\GridSize $gridSize): \Inowas\Common\Grid\ActiveCells
    {
        $areaPolygon = \geoPHP::load($area->geometry()->toJson(), 'json');
        $boundingBoxPolygon = \geoPHP::load($boundingBox->toGeoJson(), 'json');

        if(! $this->connection->fetchAssoc(sprintf('SELECT ST_Intersects(ST_GeomFromText(\'%s\'),ST_GeomFromText(\'%s\'));', $boundingBoxPolygon->asText(), $areaPolygon->asText()))){
            // AREA DOES NOT INTERSECT WITH BOUNDINGBOX
            return null;
        };

        $result = $this->connection->fetchAssoc(sprintf('SELECT ST_GeomFromText(\'%s\');', $areaPolygon->asText()));
        $areaGeometry = $result['st_geomfromtext'];



        $dX = ($boundingBox->xMax()-$boundingBox->xMin())/$gridSize->nX();
        $dY = ($boundingBox->yMax()-$boundingBox->yMin())/$gridSize->nY();

        $activeCells = [];
        $nx = $gridSize->nX();
        $ny = $gridSize->nY();

        for ($x = 0; $x<$nx; $x++){
            $activeCells[$x] = [];
            for ($y = 0; $y<$ny; $y++){

                $cellWkt = sprintf(
                    'POINT (%f %f)',
                    $boundingBox->xMin()+(($x+0.5)*$dX),
                    $boundingBox->yMax()-(($y+0.5)*$dY)
                );

                $activeCells[$x][$y] = $this->intersectWktGeometry($cellWkt, $areaGeometry);
                unset($cellWkt);
            }
        }

        return \Inowas\Common\Grid\ActiveCells::fromArray($activeCells);
    }

    private function intersectWktGeometry(string $wkt, string $geometry){

        $result = $this->connection->fetchAssoc(sprintf(
                'SELECT ST_Intersects(ST_GeomFromText(\'%s\'), \'%s\'::geometry);',
                $wkt,
                $geometry
            )
        );

        return $result['st_intersects'];
    }

    private function intersect(string $wkt1, string $wkt2){

        $result = $this->connection->fetchAssoc(sprintf(
                'SELECT ST_Intersects(ST_GeomFromText(\'%s\'),ST_GeomFromText(\'%s\'));',
                $wkt1,
                $wkt2
            )
        );

        return $result['st_intersects'];
    }

    /**
     * @param ModelObject $mo
     * @param BoundingBox $boundingBox
     * @param GridSize $gridSize
     * @return ActiveCells
     */
    public function getActiveCells(ModelObject $mo, BoundingBox $boundingBox, GridSize $gridSize)
    {
        if ($mo instanceof WellBoundary){
            return $this->getActiveCellsFromPoint($boundingBox, $gridSize, $mo->getGeometry());
        }

        $nx = $gridSize->getNX();
        $ny = $gridSize->getNY();
        $dx = ($boundingBox->getXMax()-$boundingBox->getXMin())/$nx;
        $dy = ($boundingBox->getYMax()-$boundingBox->getYMin())/$ny;
        $srid = $boundingBox->getSrid();

        $activeCells = array();
        for ($iy = 0; $iy<$ny; $iy++){
            for ($ix = 0; $ix<$nx; $ix++){
                $xMin = $boundingBox->getXMin()+$ix*$dx;
                $xMax = $boundingBox->getXMin()+$ix*$dx+$dx;
                $yMin = $boundingBox->getYMax()-$iy*$dy;
                $yMax = $boundingBox->getYMax()-$iy*$dy-$dy;

                if ($this->isActive($mo, $srid, $xMin, $xMax, $yMin, $yMax)){
                    $activeCells[$iy][$ix] = true;
                }
            }
        }

        return ActiveCells::fromArray($activeCells);
    }

    /**
     * @param ModelObject $mo
     * @param BoundingBox $boundingBox
     * @param GridSize $gridSize
     * @return ModelObject
     */
    public function setActiveCells(ModelObject $mo, BoundingBox $boundingBox, GridSize $gridSize){

        echo sprintf("Calculate active cells for Class: %s.\r\n", get_class($mo));
        $activeCells = $this->getActiveCells($mo, $boundingBox, $gridSize);
        $mo->setActiveCells($activeCells);

        return $mo;
    }

    public function getActiveCellsFromPoint(BoundingBox $bb, GridSize $gz, Point $point){

        $result = $this->getGridCellFromPoint($bb, $gz, $point);

        $cells = array();
        $cells[$result['row']][$result['col']]=true;
        return ActiveCells::fromArray($cells);
    }

    public function pointIntersectsWithArea($area, $x, $y, $srid){
        return $this->isActive($area, $srid, $x, $x, $y, $y);
    }

    public function getBoundingBoxFromPolygon(Polygon $polygon){
        $points = $polygon->getRing(0)->toArray();
        $srid = $polygon->getSrid();

        $xMin = $points[0][0];
        $xMax = $points[0][0];
        $yMin = $points[0][1];
        $yMax = $points[0][1];

        foreach ($points as $point) {
            if ($point[0]<$xMin){
                $xMin=$point[0];
            }

            if ($point[0]>$xMax){
                $xMax=$point[0];
            }

            if ($point[1]<$yMin){
                $yMin=$point[1];
            }

            if ($point[1]>$yMax){
                $yMax=$point[1];
            }
        }

        $bb = new BoundingBox($xMin, $xMax, $yMin, $yMax, $srid);

        return $this->transformBoundingBox($bb, 4326);
    }

    public function transformPoint(Point $point, $targetSrid)
    {
        if ($point->getSrid() == $targetSrid){
            return $point;
        }

        $query = $this->entityManager
            ->getConnection()
            ->prepare(sprintf('SELECT ST_AsGeoJson(ST_TRANSFORM(ST_SetSRID(ST_Point(%s, %s), %s), %s))',
                $point->getX(),
                $point->getY(),
                $point->getSrid(),
                $targetSrid
            ))
        ;

        $query->execute();
        $result = json_decode($query->fetchAll()[0]['st_asgeojson']);

        return new Point($result->coordinates[0], $result->coordinates[1], $targetSrid);
    }

    public function calculateDistanceInMetersFromTwoPoints(Point $point1, Point $point2)
    {
        $query = $this->entityManager
            ->getConnection()
            ->prepare(sprintf('SELECT ST_Distance(
                ST_Transform(ST_SetSRID(ST_Point(%s, %s), %s), %s)::geography,
                ST_Transform(ST_SetSRID(ST_Point(%s, %s), %s), %s)::geography
                )',
                $point1->getX(),
                $point1->getY(),
                $point1->getSrid(),
                4326,
                $point2->getX(),
                $point2->getY(),
                $point2->getSrid(),
                4326
            ))
        ;

        $query->execute();
        $result = $query->fetchAll();

        return (float) $result[0]["st_distance"];
    }

    public function transformBoundingBox(BoundingBox $bb, $targetSrid) {
        $lowerLeft = new Point($bb->getXMin(), $bb->getYMin(), $bb->getSrid());
        $lowerRight = new Point($bb->getXMax(), $bb->getYMin(), $bb->getSrid());
        $upperRight = new Point($bb->getXMax(), $bb->getYMax(), $bb->getSrid());

        $transformedLowerLeft = $this->transformPoint($lowerLeft, $targetSrid);
        $transformedUpperRight = $this->transformPoint($upperRight, $targetSrid);
        $dxInMeter = $this->calculateDistanceInMetersFromTwoPoints($lowerLeft, $lowerRight);
        $dyInMeter = $this->calculateDistanceInMetersFromTwoPoints($lowerRight, $upperRight);

        $bb = new BoundingBox(
            $transformedLowerLeft->getX(),
            $transformedUpperRight->getX(),
            $transformedLowerLeft->getY(),
            $transformedUpperRight->getY(),
            $targetSrid
        );

        $bb->setDXInMeters($dxInMeter);
        $bb->setDYInMeters($dyInMeter);

        return $bb;
    }

    public function getGridCellFromPoint(BoundingBox $bb, GridSize $gz, Point $point)
    {
        // Transform Point to the same Coordinate System as BoundingBox
        $point = $this->transformPoint($point, $bb->getSrid());

        // Check if point is inside of BoundingBox
        if (!($point->getX() >= $bb->getXMin()
            && $point->getX() <= $bb->getXMax()
            && $point->getY() >= $bb->getYMin()
            && $point->getY() <= $bb->getYMax())
        ) {
            return null;
        }

        $dx = ($bb->getXMax() - $bb->getXMin()) / $gz->getNX();
        $dy = ($bb->getYMax() - $bb->getYMin()) / $gz->getNY();

        $col = (int)(floor(($point->getX() - $bb->getXMin()) / $dx));
        $row = (int)($gz->getNY()-ceil(($point->getY() - $bb->getYMin()) / $dy));

        return array(
            "row" => $row,
            "col" => $col
        );
    }

    private function isActive(ModelObject $mo, int $srid, float $xMin, float $xMax, float $yMin, float $yMax){

        /** @var ModelObject $mo $className */
        $className = get_class($mo);
        $query = $this->entityManager
            ->createQuery(sprintf('SELECT ST_Intersects(ST_Envelope(ST_Makeline(ST_SetSRID(ST_POINT(:xMin, :yMin), %s), ST_SetSRID(ST_POINT(:xMax, :yMax), %s))), ST_Transform(a.geometry, %s)) FROM %s a WHERE a.id = :id', $srid, $srid, $srid, $className))
            ->setParameter('id', $mo->getId()->toString())
            ->setParameter('xMin', $xMin)
            ->setParameter('xMax', $xMax)
            ->setParameter('yMin', $yMin)
            ->setParameter('yMax', $yMax)
        ;

        return $query->getSingleScalarResult();
    }
}
