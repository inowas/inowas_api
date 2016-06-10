<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AreaRepository extends EntityRepository
{

    public function getAreaSurfaceById($id)
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT ST_Area(a.geometry) FROM AppBundle:Area a WHERE a.id = :id')
            ->setParameter('id', $id)
        ;

        return $query->getSingleScalarResult();
    }
}
