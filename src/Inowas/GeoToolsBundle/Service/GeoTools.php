<?php

namespace Inowas\GeoToolsBundle\Service;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Inowas\Common\Boundaries\AreaBoundary;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Grid\LayerNumber;

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

    public function getActiveCellsFromArea(AreaBoundary $area, BoundingBox $boundingBox, GridSize $gridSize): ?ActiveCells
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

        return ActiveCells::fromArrayAndGridSize($activeCells, $gridSize);
    }

    public function getActiveCellsFromWell(WellBoundary $well, BoundingBox $boundingBox, GridSize $gridSize): ?ActiveCells
    {
        $wellPoint = \geoPHP::load($well->geometry()->toJson(), 'json');
        $boundingBoxPolygon = \geoPHP::load($boundingBox->toGeoJson(), 'json');

        if(! $this->connection->fetchAssoc(sprintf('SELECT ST_Intersects(ST_GeomFromText(\'%s\'),ST_GeomFromText(\'%s\'));', $boundingBoxPolygon->asText(), $wellPoint->asText()))){
            // AREA DOES NOT INTERSECT WITH BOUNDINGBOX
            return null;
        };

        $dX = ($boundingBox->xMax()-$boundingBox->xMin())/$gridSize->nX();
        $dY = ($boundingBox->yMax()-$boundingBox->yMin())/$gridSize->nY();

        $nx = $gridSize->nX();
        $ny = $gridSize->nY();

        for ($x = 0; $x<$nx; $x++) {
            if (($boundingBox->xMin() + ($x * $dX) < $wellPoint->getX()) && ($boundingBox->xMin() + (($x+1) * $dX) > $wellPoint->getX())){
                break;
            }
        }

        for ($y = 0; $y<$ny; $y++) {
            if (($boundingBox->yMax() - ($y*$dY) > $wellPoint->getY()) && ($boundingBox->yMax()-(($y+1)*$dY) < $wellPoint->getY())){
                break;
            }
        };

        return ActiveCells::fromArrayGridSizeAndLayer(array($y => array($x => true)), $gridSize, $well->layerNumber());
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

    public function intersectWkt(string $wkt1, string $wkt2): bool
    {

        $result = $this->connection->fetchAssoc(sprintf(
                'SELECT ST_Intersects(ST_GeomFromText(\'%s\'),ST_GeomFromText(\'%s\'));',
                $wkt1,
                $wkt2
            )
        );

        return $result['st_intersects'];
    }

    public function getActiveCellsFromPoint(BoundingBox $bb, GridSize $gz, Point $point){

        $result = $this->getGridCellFromPoint($bb, $gz, $point);

        $cells = array();
        $cells[$result['row']][$result['col']]=true;
        return ActiveCells::fromArrayAndGridSize($cells, $gz);
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

        $bb = BoundingBox::fromCoordinates($xMin, $xMax, $yMin, $yMax, $srid);

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
        $lowerLeft = new Point($bb->xMin(), $bb->yMin(), $bb->srid());
        $upperRight = new Point($bb->xMax(), $bb->yMax(), $bb->srid());

        $transformedLowerLeft = $this->transformPoint($lowerLeft, $targetSrid);
        $transformedUpperRight = $this->transformPoint($upperRight, $targetSrid);

        $bb = BoundingBox::fromCoordinates(
            $transformedLowerLeft->getX(),
            $transformedUpperRight->getX(),
            $transformedLowerLeft->getY(),
            $transformedUpperRight->getY(),
            $targetSrid
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
}
