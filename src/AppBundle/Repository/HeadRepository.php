<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\Uuid;

class HeadRepository extends EntityRepository
{

    public function getTotim(Uuid $modelId){
        $query = $this->createQueryBuilder('hh')
            ->select('hh.totim')
            ->where('hh.modelId = :modelId')
            ->setParameter('modelId', $modelId->toString())
            ->distinct()
            ->getQuery();

        $totims = $query->getResult();
        return $totims;
    }

}
