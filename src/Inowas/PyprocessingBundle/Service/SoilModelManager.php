<?php

namespace Inowas\PyprocessingBundle\Service;

use AppBundle\Entity\SoilModel;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\SoilModelFactory;
use Doctrine\ORM\EntityManager;

class SoilModelManager
{
    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    /**
     * ModflowModelManager constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $numberOfLayers
     * @return SoilModel
     */
    public function create(int $numberOfLayers = 1){
        $soilModel = SoilModelFactory::create();
        for ($i = 0; $i < $numberOfLayers; $i++){
            $soilModel->addGeologicalLayer(GeologicalLayerFactory::create()
                ->setName('Layer'.($i+1))
                ->setOrder($i));
        }
        return $soilModel;
    }

    /**
     * @param SoilModel $soilModel
     */
    public function update(SoilModel $soilModel)
    {
        $this->entityManager->persist($soilModel);
        $this->entityManager->flush($soilModel);
    }
}