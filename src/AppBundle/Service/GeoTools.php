<?php

namespace AppBundle\Service;

use AppBundle\Entity\Area;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
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
                $query = $this->entityManager
                    ->createQuery(sprintf('SELECT ST_Intersects(ST_SetSRID(ST_Point(:x, :y), :srid), ST_Transform(a.geometry, :srid)) FROM AppBundle:Area a WHERE a.id = :id'))
                    ->setParameter('id', $id)
                    ->setParameter('srid', $srid)
                    ->setParameter('x', $boundingBox->getXMin()+$ix*$dx+$dx/2)
                    ->setParameter('y', $boundingBox->getYMax()-$iy*$dy-$dy/2)
                ;

                $activeCells[$iy][$ix] = $query->getSingleScalarResult();
            }
        }

        return $activeCells;
    }
}