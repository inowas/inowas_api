<?php

namespace Inowas\GeoTools\Model;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Inowas\Common\Boundaries\AbstractBoundary;
use Inowas\Common\Boundaries\AreaBoundary;
use Inowas\Common\Boundaries\ConstantHeadBoundary;
use Inowas\Common\Boundaries\GeneralHeadBoundary;
use Inowas\Common\Boundaries\RiverBoundary;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Geometry\AbstractGeometry;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Srid;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\GeoToolsBundle\Model\GeoTools;

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
        $geometry = \geoPHP::load($geometry->toJson(), 'json');
        $bb = $geometry->getBBox();
        return BoundingBox::fromArray($bb);
    }

    /*
    public function projectBoundingBox(BoundingBox $boundingBox, Srid $target): BoundingBox
    {
        return BoundingBox::fromArray()
    }

    public function projectGeometry(Geometry $geometry, Srid $target): Geometry
    {
        $geometry = \geoPHP::load($geometry->toJson(), 'json');
        $points = $geometry->getPoints();


        foreach ($points as $point)
        {
            $point->project()
        }
    }
    */
}
