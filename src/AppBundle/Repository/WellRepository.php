<?php

namespace AppBundle\Repository;

use \Doctrine\ORM\EntityRepository;

class WellRepository extends EntityRepository
{
    public function transformPointTo($id, $srid)
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT ST_AsGeoJson(ST_Transform(w.point, :srid)) FROM AppBundle:WellBoundary w WHERE w.id = :id')
            ->setParameter('id', $id)
            ->setParameter('srid', $srid)
        ;

        return $query->getSingleScalarResult();
    }
}
