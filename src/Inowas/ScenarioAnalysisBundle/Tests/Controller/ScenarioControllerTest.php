<?php

namespace Inowas\ScenarioAnalysisBundle\Tests\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use Inowas\AppBundle\Model\User;
use Inowas\ModflowBundle\Model\BoundaryFactory;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Service\ModflowModelManager;
use Inowas\ScenarioAnalysisBundle\Model\Events\AddWellEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellLayerNumberEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellNameEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellStressperiodsEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellTypeEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\MoveWellEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\RemoveWellEvent;
use Inowas\ScenarioAnalysisBundle\Model\Scenario;
use Inowas\ScenarioAnalysisBundle\Service\ScenarioAnalysisManager;
use Inowas\ScenarioAnalysisBundle\Service\ScenarioManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScenarioControllerTest extends WebTestCase
{

    /** @var  EntityManager */
    protected $entityManager;

    /** @var ModflowModelManager */
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
            ->get('inowas.modflow.modelmanager')
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

    public function testLoadScenariosFromModel(){

        $model = $this->modelManager->create()->setName('TestModel');
        $this->modelManager->update($model);

        $scenarioAnalysis = $this->scenarioAnalysisManager->create($model);
        $scenarioAnalysis->setUserId($this->user->getId());

        $scenario = $this->scenarioManager->create($model)->setName('TestScenarioName 1')->setDescription('TestScenarioDescription 1');
        $scenarioAnalysis->addScenario($scenario);
        $this->scenarioAnalysisManager->update($scenarioAnalysis);

        $scenario = $this->scenarioManager->create($model)->setName('TestScenarioName 2')->setDescription('TestScenarioDescription 2');
        $scenarioAnalysis->addScenario($scenario);
        $this->scenarioAnalysisManager->update($scenarioAnalysis);

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf('/api/scenarioanalysis/models/%s/scenarios.json', $model->getId()->toString()),
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->user->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $response = $client->getResponse()->getContent();
        $this->assertJson($response);
        $response = json_decode($response);
        $this->assertCount(2, $response);
        $responseScenario = $response[1];

        $this->assertObjectHasAttribute('id', $responseScenario);
        $this->assertEquals($scenario->getId()->toString(), $responseScenario->id);
        $this->assertObjectHasAttribute('name', $responseScenario);
        $this->assertEquals($scenario->getName(), $responseScenario->name);
        $this->assertObjectHasAttribute('description', $responseScenario);
        $this->assertEquals($scenario->getDescription(), $responseScenario->description);
        $this->assertObjectHasAttribute('base_model_id', $responseScenario);
        $this->assertEquals($scenario->getBaseModelId()->toString(), $responseScenario->base_model_id);
        $this->assertObjectHasAttribute('order', $responseScenario);
        $this->assertEquals($scenario->getOrder(), $responseScenario->order);
    }

    public function testPostScenario(){

        $model = $this->modelManager->create()->setName('TestModel');
        $this->modelManager->update($model);

        $scenarioAnalysis = $this->scenarioAnalysisManager->create($model);
        $scenarioAnalysis->setUserId($this->user->getId());
        $this->scenarioAnalysisManager->update($scenarioAnalysis);

        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('/api/scenarioanalysis/models/%s/scenarios.json', $model->getId()->toString()),
            array(
                'name' => 'MyScenarioName',
                'description' => 'MyScenarioDescription'
            ),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->user->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $response = $client->getResponse()->getContent();
        $this->assertJson($response);
        $responseScenario = json_decode($response);
        $this->assertObjectHasAttribute('id', $responseScenario);
        $this->assertObjectHasAttribute('name', $responseScenario);
        $this->assertEquals('MyScenarioName', $responseScenario->name);
        $this->assertObjectHasAttribute('description', $responseScenario);
        $this->assertEquals('MyScenarioDescription', $responseScenario->description);
        $this->assertObjectHasAttribute('base_model_id', $responseScenario);
        $this->assertEquals($model->getId()->toString(), $responseScenario->base_model_id);
        $this->assertObjectHasAttribute('order', $responseScenario);
        $this->assertEquals(0, $responseScenario->order);

    }

    public function testPutScenario(){

        $model = $this->modelManager->create()->setName('TestModel');
        $this->modelManager->update($model);

        $scenarioAnalysis = $this->scenarioAnalysisManager->create($model);
        $scenarioAnalysis->setUserId($this->user->getId());

        $scenario = $this->scenarioManager->create($model)->setName('TestScenarioName 1')->setDescription('TestScenarioDescription 1');
        $scenarioAnalysis->addScenario($scenario);
        $this->scenarioAnalysisManager->update($scenarioAnalysis);

        $scenario = $this->scenarioManager->create($model)->setName('TestScenarioName 2')->setDescription('TestScenarioDescription 2');
        $scenarioAnalysis->addScenario($scenario);
        $this->scenarioAnalysisManager->update($scenarioAnalysis);

        $client = static::createClient();
        $client->request(
            'PUT',
            sprintf('/api/scenarioanalysis/models/%s/scenarios/%s.json', $model->getId()->toString(), $scenario->getId()->toString()),
            array(
                'name' => 'MyScenarioName',
                'description' => 'MyScenarioDescription'
            ),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->user->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $response = $client->getResponse()->getContent();
        $this->assertJson($response);
        $responseScenario = json_decode($response);
        $this->assertObjectHasAttribute('id', $responseScenario);
        $this->assertEquals($scenario->getId()->toString(), $responseScenario->id);
        $this->assertObjectHasAttribute('name', $responseScenario);
        $this->assertEquals('MyScenarioName', $responseScenario->name);
        $this->assertObjectHasAttribute('description', $responseScenario);
        $this->assertEquals('MyScenarioDescription', $responseScenario->description);
        $this->assertObjectHasAttribute('base_model_id', $responseScenario);
        $this->assertEquals($model->getId()->toString(), $responseScenario->base_model_id);
        $this->assertObjectHasAttribute('order', $responseScenario);
        $this->assertEquals(0, $responseScenario->order);
    }

    public function testPostAddWellEventToScenario(){

        $model = $this->modelManager->create()->setName('TestModel');
        $this->modelManager->update($model);

        $scenario = $this->scenarioManager->create($model)
            ->setName('TestScenarioName 1')
            ->setDescription('TestScenarioDescription 1')
        ;
        $this->scenarioManager->update($scenario);

        $addWellPayload = new \stdClass();
        $addWellPayload->event = 'ADD_WELL';
        $addWellPayload->name = 'NewWellName';

        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('/api/scenarioanalysis/models/%s/scenarios/%s.json', $model->getId()->toString(), $scenario->getId()->toString()),
            array('payload' => json_encode($addWellPayload)),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->user->getApiKey())
        );

        $scenario = $this->scenarioManager->findById($scenario->getId());
        $this->assertCount(1, $scenario->getEvents());
        $event = $scenario->getEvents()->first();
        $this->assertInstanceOf(AddWellEvent::class, $event);
    }

    public function testPostChangeWellLayerNumberEventToScenario(){

        $model = $this->modelManager->create()->setName('TestModel');
        $well = BoundaryFactory::createWel()->setLayerNumber(1);
        $model->addBoundary($well);
        $this->modelManager->update($model);

        $scenario = $this->scenarioManager->create($model);
        $this->scenarioManager->update($scenario);

        $addWellPayload = new \stdClass();
        $addWellPayload->event = 'CHANGE_WELL_LAYER_NUMBER';
        $addWellPayload->well_id = $well->getId()->toString();
        $addWellPayload->layer_number = 1;

        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('/api/scenarioanalysis/models/%s/scenarios/%s.json', $model->getId()->toString(), $scenario->getId()->toString()),
            array('payload' => json_encode($addWellPayload)),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->user->getApiKey())
        );

        $scenario = $this->scenarioManager->findById($scenario->getId());
        $this->assertCount(1, $scenario->getEvents());
        $event = $scenario->getEvents()->first();
        $this->assertInstanceOf(ChangeWellLayerNumberEvent::class, $event);
    }

    public function testPostChangeWellNameEventToScenario(){

        $model = $this->modelManager->create()->setName('TestModel');
        $well = BoundaryFactory::createWel()->setLayerNumber(1);
        $model->addBoundary($well);
        $this->modelManager->update($model);

        $scenario = $this->scenarioManager->create($model);
        $this->scenarioManager->update($scenario);

        $addWellPayload = new \stdClass();
        $addWellPayload->event = 'CHANGE_WELL_NAME';
        $addWellPayload->well_id = $well->getId()->toString();
        $addWellPayload->name = "MyNewWellName";

        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('/api/scenarioanalysis/models/%s/scenarios/%s.json', $model->getId()->toString(), $scenario->getId()->toString()),
            array('payload' => json_encode($addWellPayload)),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->user->getApiKey())
        );

        $scenario = $this->scenarioManager->findById($scenario->getId());
        $this->assertCount(1, $scenario->getEvents());
        $event = $scenario->getEvents()->first();
        $this->assertInstanceOf(ChangeWellNameEvent::class, $event);
    }

    public function testPostChangeWellStressPeriodsEventToScenario(){

        $model = $this->modelManager->create()->setName('TestModel');
        $well = BoundaryFactory::createWel()->setLayerNumber(1);
        $model->addBoundary($well);
        $this->modelManager->update($model);

        $scenario = $this->scenarioManager->create($model);
        $this->scenarioManager->update($scenario);

        $addWellPayload = new \stdClass();
        $addWellPayload->event = 'CHANGE_WELL_STRESSPERIODS';
        $addWellPayload->well_id = $well->getId()->toString();
        $addWellPayload->stress_periods = [
            (object) array('date_time_begin' => '2015-01-01', 'flux'=> -5000),
            (object) array('date_time_begin' => '2015-02-01', 'flux'=> -6000),
            (object) array('date_time_begin' => '2015-03-01', 'flux'=> -7000),
            (object) array('date_time_begin' => '2015-04-01', 'flux'=> -8000),
            (object) array('date_time_begin' => '2015-05-01', 'flux'=> -9000)
        ];

        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('/api/scenarioanalysis/models/%s/scenarios/%s.json', $model->getId()->toString(), $scenario->getId()->toString()),
            array('payload' => json_encode($addWellPayload)),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->user->getApiKey())
        );

        $scenario = $this->scenarioManager->findById($scenario->getId());
        $this->assertCount(1, $scenario->getEvents());
        $event = $scenario->getEvents()->first();
        $this->assertInstanceOf(ChangeWellStressperiodsEvent::class, $event);
    }

    public function testPostChangeWellTypeEventToScenario(){

        $model = $this->modelManager->create()->setName('TestModel');
        $well = BoundaryFactory::createWel()->setLayerNumber(1);
        $model->addBoundary($well);
        $this->modelManager->update($model);

        $scenario = $this->scenarioManager->create($model);
        $this->scenarioManager->update($scenario);

        $addWellPayload = new \stdClass();
        $addWellPayload->event = 'CHANGE_WELL_TYPE';
        $addWellPayload->well_id = $well->getId()->toString();
        $addWellPayload->type = "wellType";

        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('/api/scenarioanalysis/models/%s/scenarios/%s.json', $model->getId()->toString(), $scenario->getId()->toString()),
            array('payload' => json_encode($addWellPayload)),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->user->getApiKey())
        );

        $scenario = $this->scenarioManager->findById($scenario->getId());
        $this->assertCount(1, $scenario->getEvents());
        $event = $scenario->getEvents()->first();
        $this->assertInstanceOf(ChangeWellTypeEvent::class, $event);
    }

    public function testPostMoveWellEventToScenario(){

        $model = $this->modelManager->create()->setName('TestModel');
        $well = BoundaryFactory::createWel()->setLayerNumber(1);
        $model->addBoundary($well);
        $this->modelManager->update($model);

        $scenario = $this->scenarioManager->create($model);
        $this->scenarioManager->update($scenario);

        $addWellPayload = new \stdClass();
        $addWellPayload->event = 'MOVE_WELL';
        $addWellPayload->well_id = $well->getId()->toString();
        $addWellPayload->geometry = (object) array('lat'=> 1, 'lng' => 2, 'srid' => 4326);

        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('/api/scenarioanalysis/models/%s/scenarios/%s.json', $model->getId()->toString(), $scenario->getId()->toString()),
            array('payload' => json_encode($addWellPayload)),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->user->getApiKey())
        );

        $scenario = $this->scenarioManager->findById($scenario->getId());
        $this->assertCount(1, $scenario->getEvents());
        $event = $scenario->getEvents()->first();
        $this->assertInstanceOf(MoveWellEvent::class, $event);
    }

    public function testPostRemoveWellEventToScenario(){

        $model = $this->modelManager->create()->setName('TestModel');
        $well = BoundaryFactory::createWel()->setLayerNumber(1);
        $model->addBoundary($well);
        $this->modelManager->update($model);

        $scenario = $this->scenarioManager->create($model);
        $this->scenarioManager->update($scenario);

        $addWellPayload = new \stdClass();
        $addWellPayload->event = 'REMOVE_WELL';
        $addWellPayload->well_id = $well->getId()->toString();

        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('/api/scenarioanalysis/models/%s/scenarios/%s.json', $model->getId()->toString(), $scenario->getId()->toString()),
            array('payload' => json_encode($addWellPayload)),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->user->getApiKey())
        );

        $scenario = $this->scenarioManager->findById($scenario->getId());
        $this->assertCount(1, $scenario->getEvents());
        $event = $scenario->getEvents()->first();
        $this->assertInstanceOf(RemoveWellEvent::class, $event);
    }

    public function tearDown(){
        $models = $this->modelManager->findAll();

        /** @var ModflowModel $model */
        foreach ($models as $model)
        {
            $scenarios = $this->scenarioManager->findByModelId($model->getId());

            /** @var Scenario $scenario */
            foreach ($scenarios as $scenario){
                $this->scenarioManager->remove($scenario->getId());
            }
            $this->modelManager->remove($model);
        }

        $users = $this->userManager->findUsers();
        foreach ($users as $user){
            $this->userManager->deleteUser($user);
        }
    }
}
