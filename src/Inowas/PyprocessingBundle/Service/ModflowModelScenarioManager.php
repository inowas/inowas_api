<?php

namespace Inowas\PyprocessingBundle\Service;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\ModflowModelScenario;
use AppBundle\Model\ModelScenarioFactory;
use Doctrine\ORM\EntityManager;
use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;
use Ramsey\Uuid\Uuid;

class ModflowModelScenarioManager
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
     * @param ModFlowModel $model
     * @return ModflowModelScenario
     */
    public function create(ModFlowModel $model){
        return ModelScenarioFactory::create($model);
    }

    /**
     * @param ModflowModelScenario $scenario
     * @return ModflowModelScenario
     */
    public function update(ModflowModelScenario $scenario)
    {
        $this->persist($scenario);
        return $scenario;
    }

    /**
     * @param ModflowModelScenario $scenario
     */
    public function persist(ModflowModelScenario $scenario){
        $this->entityManager->persist($scenario);
        $this->entityManager->flush();
    }

    /**
     * @param ModflowModelScenario $scenario
     */
    public function remove(ModflowModelScenario $scenario){
        $this->entityManager->remove($scenario);
        $this->entityManager->flush();
    }

    /**
     * @param $id
     * @return ModflowModelScenario|null
     */
    public function findById($id){
        if (! Uuid::isValid($id)){
            throw new InvalidArgumentException('The given id is not a valid Uuid.');
        }

        return $this->entityManager->getRepository('AppBundle:ModflowModelScenario')
            ->findOneBy(array(
                'id' => $id
            ));
    }
}
