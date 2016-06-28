<?php

namespace AppBundle\Repository;

use \Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class SoilModelRepository extends EntityRepository
{
    public function findByLayerId($layerId)
    {
        $query = $this->createQueryBuilder('sm')
            ->leftJoin('sm.modelObjects', 'mo')
            ->where('mo.id = :id')
            ->setParameter('id', $layerId)
            ->getQuery();

        try {
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
