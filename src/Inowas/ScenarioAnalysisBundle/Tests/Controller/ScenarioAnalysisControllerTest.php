<?php

namespace Inowas\ScenarioAnalysisBundle\Tests\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use Inowas\AppBundle\Model\User;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Service\ModflowToolManager;
use Inowas\ScenarioAnalysisBundle\Model\Scenario;
use Inowas\ScenarioAnalysisBundle\Service\ScenarioAnalysisManager;
use Inowas\ScenarioAnalysisBundle\Service\ScenarioManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScenarioAnalysisControllerTest extends WebTestCase
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

    public function testGetScenarioAnalysis(){

        $model = $this->modelManager->createModel()->setName('TestModel')->setDescription('Description');
        $this->modelManager->updateModel($model);

        $scenarioAnalysis = $this->scenarioAnalysisManager->create($this->user, $model);
        $scenario = $this->scenarioManager->create($model)->setName('TestScenarioName 1')->setDescription('TestScenarioDescription 1');
        $scenarioAnalysis->addScenario($scenario);
        $this->scenarioAnalysisManager->update($scenarioAnalysis);

        $scenario = $this->scenarioManager->create($model)->setName('TestScenarioName 2')->setDescription('TestScenarioDescription 2');
        $scenarioAnalysis->addScenario($scenario);
        $this->scenarioAnalysisManager->update($scenarioAnalysis);

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf('/api/scenarioanalysis/models/%s.json', $model->getId()->toString()),
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->user->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $response = $client->getResponse()->getContent();
        $this->assertJson($response);
        $response = json_decode($response);
        $this->assertObjectHasAttribute('base_model', $response);
        $this->assertObjectHasAttribute('id', $response->base_model);
        $this->assertEquals($model->getId()->toString(), $response->base_model->id);
        $this->assertObjectHasAttribute('name', $response->base_model);
        $this->assertEquals($model->getName(), $response->base_model->name);
        $this->assertObjectHasAttribute('description', $response->base_model);
        $this->assertEquals($model->getDescription(), $response->base_model->description);
        $this->assertObjectHasAttribute('scenarios', $response);
        $this->assertCount(2, $response->scenarios);
    }

    public function testGetScenarioAnalysisByUserName(){

        $model = $this->modelManager->createModel()->setName('TestModel')->setDescription('Description');
        $this->modelManager->updateModel($model);

        $scenarioAnalysis = $this->scenarioAnalysisManager->create($this->user, $model);
        $scenario = $this->scenarioManager->create($model)->setName('TestScenarioName 1')->setDescription('TestScenarioDescription 1');
        $scenarioAnalysis->addScenario($scenario);
        $this->scenarioAnalysisManager->update($scenarioAnalysis);

        $scenario = $this->scenarioManager->create($model)->setName('TestScenarioName 2')->setDescription('TestScenarioDescription 2');
        $scenarioAnalysis->addScenario($scenario);
        $this->scenarioAnalysisManager->update($scenarioAnalysis);

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf('/api/scenarioanalysis/users/%s.json', $this->user->getUsername()),
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->user->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $response = $client->getResponse()->getContent();
        $this->assertJson($response);
        $response = json_decode($response);
        $this->assertTrue(is_array($response));
        $this->assertCount(1, $response);
        $response = $response[0];
        $this->assertObjectHasAttribute('base_model', $response);
        $this->assertObjectHasAttribute('id', $response->base_model);
        $this->assertObjectHasAttribute('scenarios', $response);
        $this->assertCount(2, $response->scenarios);
    }


    public function tearDown(){
        $models = $this->modelManager->findAllModels();

        /** @var ModflowModel $model */
        foreach ($models as $model)
        {
            $scenarios = $this->scenarioManager->findByModelId($model->getId());

            /** @var Scenario $scenario */
            foreach ($scenarios as $scenario){
                $this->scenarioManager->remove($scenario->getId());
            }
            $this->modelManager->removeModel($model);
        }

        $users = $this->userManager->findUsers();
        foreach ($users as $user){
            $this->userManager->deleteUser($user);
        }
    }
}
