<?php

namespace Inowas\ScenarioAnalysisBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\Uuid;

class ScenarioAnalysisRepository extends EntityRepository
{
    public function findScenarioAnalysisByScenarioId(Uuid $id)
    {
        $query = $this->createQueryBuilder('sa')
            ->leftJoin('sa.scenarios', 'sc')
            ->where('sc.id = :id')
            ->setParameter('id', $id)
            ->getQuery();


        return $query->getSingleResult();
    }
}