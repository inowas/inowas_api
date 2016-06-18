<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AreaRepository extends EntityRepository
{

    public function getAreaSurfaceById($id)
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT ST_Area(ST_Transform(a.geometry, 3857)) FROM AppBundle:Area a WHERE a.id = :id')
            ->setParameter('id', $id)
        ;

        return $query->getSingleScalarResult();
    }

    public function getAreaPolygonIn4326($id)
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT ST_AsGeoJson(ST_Transform(a.geometry, 4326)) FROM AppBundle:Area a WHERE a.id = :id')
            ->setParameter('id', $id)
        ;

        return $query->getSingleScalarResult();
    }
}
