<?php

namespace AppBundle\Service;

use AppBundle\Entity\Area;
use AppBundle\Entity\ModelObject;
use AppBundle\Model\ActiveCells;
use AppBundle\Model\GeoJson\Feature;
use AppBundle\Model\GeoJson\FeatureCollection;
use AppBundle\Model\GeoJson\Polygon;
use AppBundle\Model\GeoJson\Properties;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
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
     * @param Area $area
     * @param BoundingBox $boundingBox
     * @param GridSize $gridSize
     * @return ActiveCells
     */
    public function calculateActiveCells(Area $area, BoundingBox $boundingBox, GridSize $gridSize)
    {
        $nx = $gridSize->getNX();
        $ny = $gridSize->getNY();
        $dx = ($boundingBox->getXMax()-$boundingBox->getXMin())/$nx;
        $dy = ($boundingBox->getYMax()-$boundingBox->getYMin())/$ny;
        $id = $area->getId()->toString();
        $srid = $boundingBox->getSrid();

        $activeCells = array();
        for ($iy = 0; $iy<$ny; $iy++){
            for ($ix = 0; $ix<$nx; $ix++){
                $x = $boundingBox->getXMin()+$ix*$dx+$dx/2;
                $y = $boundingBox->getYMax()-$iy*$dy-$dy/2;
                $activeCells[$iy][$ix] = $this->isActive($id, $srid, $x, $y);
            }
        }

        return ActiveCells::fromArray($activeCells);
    }

    private function isActive($id, $srid, $x, $y){
        $query = $this->entityManager
            ->createQuery(sprintf('SELECT ST_Intersects(ST_SetSRID(ST_Point(:x, :y), :srid), ST_Transform(a.geometry, :srid)) FROM AppBundle:Area a WHERE a.id = :id'))
            ->setParameter('id', $id)
            ->setParameter('srid', $srid)
            ->setParameter('x', $x)
            ->setParameter('y', $y)
        ;

        return $query->getSingleScalarResult();
    }

    public function pointIntersectsWithArea($areaId, $x, $y, $srid){
        return $this->isActive($areaId, $srid, $x, $y);
    }

    public function getGeoJsonGrid(BoundingBox $boundingBox, GridSize $gridSize, $activeCells)
    {
        $nx = $gridSize->getNX();
        $ny = $gridSize->getNY();
        $dx = ($boundingBox->getXMax()-$boundingBox->getXMin())/$nx;
        $dy = ($boundingBox->getYMax()-$boundingBox->getYMin())/$ny;

        $featureCollection = new FeatureCollection();

        $i = 0;
        for ($iy = 0; $iy<$ny; $iy++){
            for ($ix = 0; $ix<$nx; $ix++){

                if ($activeCells[$iy][$ix]){
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
            ->createQuery('SELECT ST_AsGeoJson(ST_Transform(a.geometry, :srid)) FROM '.$className.' a WHERE a.id = :id')
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

    public function transformBoundingBox(BoundingBox $bb, $targetSrid)
    {
        if ($bb->getSrid() == $targetSrid){
            return $bb;
        }

        $lowerLeft = new Point($bb->getXMin(), $bb->getYMin(), $bb->getSrid());
        $upperRight = new Point($bb->getXMax(), $bb->getYMax(), $bb->getSrid());

        $transformedLowerLeft = $this->transformPoint($lowerLeft, $targetSrid);
        $transformedUpperRight = $this->transformPoint($upperRight, $targetSrid);

        return new BoundingBox(
            $transformedLowerLeft->getX(),
            $transformedUpperRight->getX(),
            $transformedLowerLeft->getY(),
            $transformedUpperRight->getY(),
            $targetSrid
        );
    }

    public function getGridCellFromPoint(BoundingBox $bb, GridSize $gz, Point $point)
    {
        // Transform Point to the same Coordiate System as Boundingbox
        $point = $this->transformPoint($point, $bb->getSrid());

        // Check if point is inside of BoundingBox
        if (!($point->getX() >= $bb->getXMin()
            && $point->getX() <= $bb->getXMax()
            && $point->getY() >= $bb->getYMin()
            && $point->getY() <= $bb->getYMax())
        ) {
            return array(
                "row" => null,
                "col" => null
            );
        }


        $dx = ($bb->getXMax() - $bb->getXMin()) / $gz->getNX();
        $dy = ($bb->getYMax() - $bb->getYMin()) / $gz->getNY();

        $col = (int)(floor(($point->getX() - $bb->getXMin()) / $dx));
        $row = (int)($gz->getNY()-floor(($point->getY() - $bb->getYMin()) / $dy));

        return array(
            "row" => $row,
            "col" => $col
        );
    }

}