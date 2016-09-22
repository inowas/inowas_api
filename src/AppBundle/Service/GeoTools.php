<?php

namespace AppBundle\Service;

use AppBundle\Entity\ModelObject;
use AppBundle\Entity\WellBoundary;
use AppBundle\Model\ActiveCells;
use AppBundle\Model\GeoJson\Feature;
use AppBundle\Model\GeoJson\FeatureCollection;
use AppBundle\Model\GeoJson\Polygon;
use AppBundle\Model\GeoJson\Properties;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\Point;
use Doctrine\ORM\EntityManager;

class GeoTools
{

    /** @var  EntityManager */
    protected $entityManager;

    /**
     * GeoTools constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
                $xmin = $boundingBox->getXMin()+$ix*$dx;
                $xmax = $boundingBox->getXMin()+$ix*$dx+$dx;
                $ymin = $boundingBox->getYMax()-$iy*$dy;
                $ymax = $boundingBox->getYMax()-$iy*$dy-$dy;

                if ($this->isActive($mo, $srid, $xmin, $xmax, $ymin, $ymax)){
                    $activeCells[$iy][$ix] = true;
                }
            }
        }

        return ActiveCells::fromArray($activeCells);
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

    public function getBoundingBoxFromPolygon(\CrEOF\Spatial\PHP\Types\Geometry\Polygon $polygon){
        $points = $polygon->getRing(0)->toArray();
        $srid = $polygon->getSrid();

        $xmin = $points[0][0];
        $xmax = $points[0][0];
        $ymin = $points[0][1];
        $ymax = $points[0][1];

        foreach ($points as $point) {
            if ($point[0]<$xmin){
                $xmin=$point[0];
            }

            if ($point[0]>$xmax){
                $xmax=$point[0];
            }

            if ($point[1]<$ymin){
                $ymin=$point[1];
            }

            if ($point[1]>$ymax){
                $ymax=$point[1];
            }
        }

        $bb = new BoundingBox($xmin, $xmax, $ymin, $ymax, $srid);

        return $this->transformBoundingBox($bb, 4326);
    }

    public function getGeoJsonGrid(BoundingBox $boundingBox, GridSize $gridSize, ActiveCells $activeCells)
    {
        $nx = $gridSize->getNX();
        $ny = $gridSize->getNY();
        $dx = ($boundingBox->getXMax()-$boundingBox->getXMin())/$nx;
        $dy = ($boundingBox->getYMax()-$boundingBox->getYMin())/$ny;
        $activeCells = $activeCells->toArray();

        $featureCollection = new FeatureCollection();

        $i = 0;
        for ($iy = 0; $iy<$ny; $iy++){
            for ($ix = 0; $ix<$nx; $ix++){

                if (is_array($activeCells[$iy]) && key_exists($ix, $activeCells[$iy])){
                    $xMin= $boundingBox->getXMin()+$ix*$dx;
                    $xMax= $boundingBox->getXMin()+$ix*$dx+$dx;
                    $yMin= $boundingBox->getYMax()-$iy*$dy-$dy;
                    $yMax= $boundingBox->getYMax()-$iy*$dy;
                    $feature = new Feature($i);
                    $polygon = new Polygon();
                    $polygon->setCoordinates(array(
                        array(
                            array($xMin, $yMin),
                            array($xMin, $yMax),
                            array($xMax, $yMax),
                            array($xMax, $yMin),
                            array($xMin, $yMin)
                        )
                    ));
                    $feature->setGeometry($polygon);
                    $featureCollection->addFeature($feature);

                    $properties = new Properties();
                    $properties->row = $iy;
                    $properties->col = $ix;
                    $feature->setProperties($properties);
                    $i++;
                }
            }
        }

        return $featureCollection;
    }

    public function getGeometryFromModelObjectAsGeoJSON(ModelObject $mo, $targetSrid)
    {
        $id = $mo->getId()->toString();
        $className = $mo->getNameOfClass();

        $query = $this->entityManager
            ->createQuery(sprintf('SELECT ST_AsGeoJson(ST_Transform(a.geometry, :srid)) FROM %s a WHERE a.id = :id', $className))
            ->setParameter('id', $id)
            ->setParameter('srid', $targetSrid)
        ;

        return $query->getSingleScalarResult();
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

    public function transformBoundingBox(BoundingBox $bb, $targetSrid)
    {
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
        $className = $mo->getNameOfClass();
        $query = $this->entityManager
            ->createQuery(sprintf('SELECT ST_Intersects(ST_Envelope(ST_Makeline(ST_SetSRID(ST_POINT(:xMin, :yMin), :srid), ST_SetSRID(ST_POINT(:xMax, :yMax), :srid))), ST_Transform(a.geometry, :srid)) FROM %s a WHERE a.id = :id', $className))
            ->setParameter('id', $mo->getId()->toString())
            ->setParameter('srid', $srid)
            ->setParameter('xMin', $xMin)
            ->setParameter('xMax', $xMax)
            ->setParameter('yMin', $yMin)
            ->setParameter('yMax', $yMax)
        ;

        return $query->getSingleScalarResult();
    }

}