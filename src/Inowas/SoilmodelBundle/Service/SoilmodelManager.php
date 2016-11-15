<?php

namespace Inowas\Soilmodel\Service;

use Doctrine\ORM\EntityManager;
use Inowas\Soilmodel\Model\Soilmodel;
use Inowas\Soilmodel\Model\SoilmodelFactory;

class SoilmodelManager
{
    /** @var  EntityManager $em */
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function create(){
        return SoilmodelFactory::create();
    }

    public function update(Soilmodel $soilmodel){
        $this->em->persist($soilmodel);
        $this->em->flush();
    }
}