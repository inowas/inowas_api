<?php

namespace Inowas\ScenarioAnalysisBundle\Tests\Service;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use Inowas\AppBundle\Model\User;
use Inowas\ModflowBundle\Service\ModflowToolManager;
use Inowas\ScenarioAnalysisBundle\Service\ScenarioAnalysisManager;
use Inowas\ScenarioAnalysisBundle\Service\ScenarioManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScenarioAnalysisManagerTest extends WebTestCase
{


    /** @var  EntityManager */
    protected $entityManager;

    /** @var ModflowToolManager */
    protected $modelManager;

    /** @var ScenarioManager */
    protected $scenarioManager;

    /** @var ScenarioAnalysisManager */
    protected $scenarioAnalysisManager;

    /** @var UserManager */
    protected $userManager;

    /** @var User */
    protected $user;

    public function setUp()
    {
        self::bootKernel();

        $this->modelManager = static::$kernel->getContainer()
            ->get('inowas.modflow.toolmanager')
        ;

        $this->scenarioAnalysisManager = static::$kernel->getContainer()
            ->get('inowas.scenarioanalysis.scenarioanalysismanager')
        ;

        $this->scenarioManager = static::$kernel->getContainer()
            ->get('inowas.scenarioanalysis.scenariomanager')
        ;

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->userManager = static::$kernel->getContainer()
            ->get('fos_user.user_manager')
        ;

        $this->user = $this->userManager->findUserByUsername('testUser');
        if(! $this->user instanceof User){
            $this->user = $this->userManager->createUser();
            $this->user->setUsername('testUser');
            $this->user->setEmail('testUser@testUser.com');
            $this->user->setPlainPassword('testUserPassword');
            $this->user->setEnabled(true);
            $this->userManager->updateUser($this->user);
        }
    }

    public function testFindApiKeyByScenarioId()
    {
        $model = $this->modelManager->createModel();
        $this->modelManager->updateModel($model);
        $scenario = $this->scenarioManager->create($model);
        $scenarioAnalysis = $this->scenarioAnalysisManager->create($this->user, $model);
        $scenarioAnalysis->addScenario($scenario);
        $this->scenarioAnalysisManager->update($scenarioAnalysis);
        $apiKey = $this->user->getApiKey();
        $this->assertEquals($apiKey, $this->scenarioAnalysisManager->findApiKeyByScenarioId($scenario->getId()));
    }

    public function tearDown()
    {
        $models = $this->entityManager
            ->getRepository('InowasModflowBundle:ModflowModel')
            ->findAll();

        foreach ($models as $model){
            $this->entityManager->remove($model);
        }

        $scenarios = $this->entityManager
            ->getRepository('InowasScenarioAnalysisBundle:Scenario')
            ->findAll();

        foreach ($scenarios as $scenario){
            $this->entityManager->remove($scenario);
        }


        $scenarioAnalyses = $this->entityManager
            ->getRepository('InowasScenarioAnalysisBundle:ScenarioAnalysis')
            ->findAll();

        foreach ($scenarioAnalyses as $scenarioAnalyse){
            $this->entityManager->remove($scenarioAnalyse);
        }
    }
}
