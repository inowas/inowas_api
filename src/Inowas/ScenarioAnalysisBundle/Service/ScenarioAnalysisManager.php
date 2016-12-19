<?php

namespace Inowas\ScenarioAnalysisBundle\Service;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserInterface;
use Inowas\AppBundle\Model\User;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidArgumentException;
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

    public function findApiKeyByScenarioId(Uuid $id){
        $sa = $this->entityManager
            ->getRepository('InowasScenarioAnalysisBundle:ScenarioAnalysis')
            ->findScenarioAnalysisByScenarioId($id);

        if (! $sa instanceof ScenarioAnalysis){
            throw new InvalidArgumentException(sprintf('Scenarioanalysis with Id=%s not found.', $id));
        }

        $userId = $sa->getUserId();
        $user = $this->entityManager->getRepository('InowasAppBundle:User')
            ->findOneBy(array(
                'id' => $userId
            ));

        if (! $user instanceof User){
            throw new InvalidArgumentException(sprintf('User with Id=%s not found.', $userId));
        }

        return $user->getApiKey();
    }
}
