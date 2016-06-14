<?php

namespace AppBundle\Repository;

class WellRepository extends \Doctrine\ORM\EntityRepository
{
    public function transformPointTo($id, $srid)
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT ST_AsGeoJson(ST_Transform(w.point, :srid)) FROM AppBundle:Well w WHERE w.id = :id')
            ->setParameter('id', $id)
            ->setParameter('srid', $srid)
        ;

        return $query->getSingleScalarResult();
    }
}
