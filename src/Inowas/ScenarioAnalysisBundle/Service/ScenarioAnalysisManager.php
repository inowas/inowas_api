<?php

namespace Inowas\ScenarioAnalysisBundle\Service;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserInterface;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ScenarioAnalysisBundle\Factory\ScenarioAnalysisFactory;
use Inowas\ScenarioAnalysisBundle\Model\ScenarioAnalysis;
use Ramsey\Uuid\Uuid;

class ScenarioAnalysisManager
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

    public function findByUserId(Uuid $userId){
        return $this->entityManager->getRepository('InowasScenarioAnalysisBundle:ScenarioAnalysis')
            ->findBy(array(
                'userId' => $userId
            ));
    }

    public function findByUserIdAndBasemodelId(Uuid $userId, Uuid $id){
        return $this->entityManager->getRepository('InowasScenarioAnalysisBundle:ScenarioAnalysis')
            ->findOneBy(array(
                'baseModelId' => $id,
                'userId' => $userId
            ));
    }

    public function create(UserInterface $user, ModflowModel $baseModel){
        return ScenarioAnalysisFactory::create($user, $baseModel);
    }

    /**
     * @param ScenarioAnalysis $scenarioAnalysis
     * @return ScenarioAnalysis
     */
    public function update(ScenarioAnalysis $scenarioAnalysis)
    {
        $this->entityManager->persist($scenarioAnalysis);
        $this->entityManager->flush();
        return $scenarioAnalysis;
    }
}
