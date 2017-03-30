<?php

namespace AppBundle\Service;

use AppBundle\Entity\ModelObject;
use AppBundle\Entity\WellBoundary;
use AppBundle\Model\GeoJson\Feature;
use AppBundle\Model\GeoJson\FeatureCollection;
use AppBundle\Model\GeoJson\Polygon;
use AppBundle\Model\GeoJson\Properties;
use Doctrine\ORM\EntityManager;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;

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

        $nx = $gridSize->nX();
        $ny = $gridSize->nY();
        $dx = ($boundingBox->xMax()-$boundingBox->xMin())/$nx;
        $dy = ($boundingBox->yMax()-$boundingBox->yMin())/$ny;
        $srid = $boundingBox->srid();

        $activeCells = array();
        for ($iy = 0; $iy<$ny; $iy++){
            for ($ix = 0; $ix<$nx; $ix++){
                $xMin = $boundingBox->xMin()+$ix*$dx;
                $xMax = $boundingBox->xMin()+$ix*$dx+$dx;
                $yMin = $boundingBox->yMax()-$iy*$dy;
                $yMax = $boundingBox->yMax()-$iy*$dy-$dy;

                if ($this->isActive($mo, $srid, $xMin, $xMax, $yMin, $yMax)){
                    $activeCells[$iy][$ix] = true;
                }
            }
        }

        return ActiveCells::fromArrayAndGridSize($activeCells, $gridSize);
    }

    /**
     * @param ModelObject $mo
     * @param BoundingBox $boundingBox
     * @param GridSize $gridSize
     * @return ModelObject
     */
    public function setActiveCells(ModelObject $mo, BoundingBox $boundingBox, GridSize $gridSize){

        echo sprintf("Calculate active cells for Class: %s.\r\n", get_class($mo));
        #$activeCells = $this->getActiveCells($mo, $boundingBox, $gridSize);
        #$mo->setActiveCells($activeCells);

        return $mo;
    }

    public function getActiveCellsFromPoint(BoundingBox $bb, GridSize $gz, Point $point): ActiveCells
    {
        $result = $this->getGridCellFromPoint($bb, $gz, $point);
        $cells = array();
        $cells[$result['row']][$result['col']] = true;
        return ActiveCells::fromArrayAndGridSize($cells, $gz);
    }

    public function pointIntersectsWithArea($area, $x, $y, $srid){
        return $this->isActive($area, $srid, $x, $x, $y, $y);
    }

    public function getBoundingBoxFromPolygon(\CrEOF\Spatial\PHP\Types\Geometry\Polygon $polygon){
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

        $bb = BoundingBox::fromCoordinates($xMin, $xMax, $yMin, $yMax, $srid);

        return $this->transformBoundingBox($bb, 4326);
    }

    public function getGeoJsonGrid(BoundingBox $boundingBox, GridSize $gridSize, ActiveCells $activeCells)
    {
        $nx = $gridSize->nX();
        $ny = $gridSize->nY();
        $dx = ($boundingBox->xMax()-$boundingBox->xMin())/$nx;
        $dy = ($boundingBox->yMax()-$boundingBox->yMin())/$ny;
        $activeCells = $activeCells->cells();

        $featureCollection = new FeatureCollection();

        $i = 0;
        for ($iy = 0; $iy<$ny; $iy++){
            for ($ix = 0; $ix<$nx; $ix++){

                if (is_array($activeCells[$iy]) && key_exists($ix, $activeCells[$iy])){
                    $xMin= $boundingBox->xMin()+$ix*$dx;
                    $xMax= $boundingBox->xMin()+$ix*$dx+$dx;
                    $yMin= $boundingBox->yMax()-$iy*$dy-$dy;
                    $yMax= $boundingBox->yMax()-$iy*$dy;
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
        $lowerLeft = new Point($bb->xMin(), $bb->yMin(), $bb->srid());
        $lowerRight = new Point($bb->xMax(), $bb->yMin(), $bb->srid());
        $upperRight = new Point($bb->xMax(), $bb->yMax(), $bb->srid());

        $transformedLowerLeft = $this->transformPoint($lowerLeft, $targetSrid);
        $transformedUpperRight = $this->transformPoint($upperRight, $targetSrid);
        $dxInMeter = round($this->calculateDistanceInMetersFromTwoPoints($lowerLeft, $lowerRight));
        $dyInMeter = round($this->calculateDistanceInMetersFromTwoPoints($lowerRight, $upperRight));

        $bb = BoundingBox::fromCoordinates(
            $transformedLowerLeft->getX(),
            $transformedUpperRight->getX(),
            $transformedLowerLeft->getY(),
            $transformedUpperRight->getY(),
            $targetSrid,
            $dxInMeter,
            $dyInMeter
        );

        return $bb;
    }

    public function getGridCellFromPoint(BoundingBox $bb, GridSize $gz, Point $point)
    {
        // Transform Point to the same Coordinate System as BoundingBox
        $point = $this->transformPoint($point, $bb->srid());

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

    private function isActive(ModelObject $mo, int $srid, float $xMin, float $xMax, float $yMin, float $yMax){

        /** @var ModelObject $mo $className */
        $className = $mo->getNameOfClass();
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
