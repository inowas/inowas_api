<?php

namespace Inowas\ScenarioAnalysisBundle\Service;

use Doctrine\ORM\EntityManager;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidArgumentException;
use Inowas\ScenarioAnalysisBundle\Factory\ScenarioFactory;
use Inowas\ScenarioAnalysisBundle\Model\Scenario;
use Ramsey\Uuid\Uuid;

class ScenarioManager
{
    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param ModflowModel $model
     * @return Scenario
     */
    public function create(ModflowModel $model)
    {
        $scenario = ScenarioFactory::create($model);
        return $scenario;
    }

    /**
     * @param $id
     * @return Scenario|null
     * @throws InvalidArgumentException
     */
    public function findByModelId($id)
    {
        if (! Uuid::isValid($id)){
            throw new InvalidArgumentException('The given id is not a valid Uuid.');
        }

        return $this->entityManager
            ->getRepository('InowasScenarioAnalysisBundle:Scenario')
            ->findBy(array(
                'baseModelId' => $id
            ));
    }

    /**
     * @param $id
     * @return Scenario|null
     * @throws InvalidArgumentException
     */
    public function findById($id)
    {
        if (! Uuid::isValid($id)){
            throw new InvalidArgumentException('The given id is not a valid Uuid.');
        }

        return $this->entityManager
            ->getRepository('InowasScenarioAnalysisBundle:Scenario')
            ->findOneBy(array(
                'id' => $id
            ));
    }

    /**
     * @param Uuid $id
     */
    public function remove($id)
    {
        $scenario = $this->findById($id);

        if ($scenario instanceof Scenario){
            $this->entityManager->remove($scenario);
            $this->entityManager->flush();
        }
    }

    /**
     * @param Scenario $scenario
     * @return Scenario
     */
    public function update(Scenario $scenario)
    {
        $this->entityManager->persist($scenario);
        $this->entityManager->flush();
        return $scenario;
    }
}
