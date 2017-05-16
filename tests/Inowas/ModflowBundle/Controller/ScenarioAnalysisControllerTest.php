<?php

declare(strict_types=1);

namespace Tests\Inowas\ModflowBundle\Controller;

use FOS\UserBundle\Doctrine\UserManager;
use Inowas\AppBundle\Model\User;
use Inowas\Common\Boundaries\Area;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Modflow\Modelname;
use Inowas\Common\Modflow\ModflowModelDescription;
use Inowas\Common\Projection\ProjectionInterface;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelDescription;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelName;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\ScenarioAnalysis\Infrastructure\Projection\ScenarioAnalysisFinder;
use Inowas\ScenarioAnalysis\Model\Command\AddScenario;
use Inowas\ScenarioAnalysis\Model\Command\CreateScenarioAnalysis;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Prooph\ServiceBus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScenarioAnalysisControllerTest extends WebTestCase
{
    /** @var UserManager */
    protected $userManager;

    /** @var User */
    protected $user;

    /** @var  CommandBus */
    protected $commandBus;

    /** @var  ProjectionInterface */
    protected $projection;

    /** @var  ScenarioAnalysisFinder */
    protected $modelScenarioFinder;

    /** @var  ModflowId */
    protected $modelId;

    /** @var  UserId */
    protected $userId;

    /** @var  ScenarioAnalysisId */
    protected $scenarioAnalysisId;

    /** @var  ModflowId */
    protected $scenarioId;

    public function setUp()
    {
        self::bootKernel();

        $this->userManager = static::$kernel->getContainer()
            ->get('fos_user.user_manager');

        $this->commandBus = static::$kernel->getContainer()
            ->get('prooph_service_bus.modflow_command_bus');

        $this->projection = static::$kernel->getContainer()
            ->get('inowas.scenarioanalysis.scenarioanalysis_list_projector');

        $this->projection->reset();

        /** @var ScenarioAnalysisFinder modelScenarioFinder */
        $this->modelScenarioFinder = static::$kernel->getContainer()
            ->get('inowas.scenarioanalysis.scenarioanalysis_finder');

        $this->user = $this->userManager->findUserByUsername('testUser');

        if(! $this->user instanceof User){
            $this->user = $this->userManager->createUser();
            $this->user->setUsername('testUser');
            $this->user->setEmail('testUser@testUser.com');
            $this->user->setPlainPassword('testUserPassword');
            $this->user->setEnabled(true);
            $this->userManager->updateUser($this->user);
        }

        $this->modelId = ModflowId::generate();

        $this->userId = UserId::fromString($this->user->getId()->toString());
        $this->commandBus->dispatch(CreateModflowModel::newWithId($this->userId, $this->modelId, $this->getArea(), GridSize::fromXY(10,20)));
        $this->commandBus->dispatch(ChangeModflowModelName::forModflowModel($this->userId, $this->modelId, Modelname::fromString('TestName')));
        $this->commandBus->dispatch(ChangeModflowModelDescription::forModflowModel($this->userId, $this->modelId, ModflowModelDescription::fromString('TestDescription')));

        $this->scenarioAnalysisId = ScenarioAnalysisId::generate();
        $this->commandBus->dispatch(CreateScenarioAnalysis::byUserwithBaseModel($this->scenarioAnalysisId, $this->userId, $this->modelId));

        $this->scenarioId = ModflowId::generate();
        $this->commandBus->dispatch(AddScenario::byUserWithBaseModelAndScenarioId($this->scenarioAnalysisId, $this->userId, $this->modelId, $this->scenarioId));
        $this->commandBus->dispatch(ChangeModflowModelName::forModflowModel($this->userId, $this->scenarioId, Modelname::fromString('Scenario_1')));
        $this->commandBus->dispatch(ChangeModflowModelDescription::forModflowModel($this->userId, $this->scenarioId, ModflowModelDescription::fromString('Scenario_Description_1')));
    }

    /**
     * @test
     */
    public function it_loads_the_model_from_the_projection()
    {
        $result = $this->modelScenarioFinder->findAll();
        $this->assertCount(1, $result);
        $firstResult = $result[0];
        $this->assertArrayHasKey('scenarios', $firstResult);
        $scenarios = json_decode($firstResult['scenarios']);
        $this->assertCount(1, $scenarios);
        $this->assertEquals($this->scenarioId->toString(), $scenarios[0]);
    }

    /**
     * @test
     */
    public function the_user_can_access_a_my_projects_pages()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/scenarioanalysis/my/projects',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->user->getApiKey())
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function tearDown()
    {
        $users = $this->userManager->findUsers();
        foreach ($users as $user){
            $this->userManager->deleteUser($user);
        }
    }

    private function getArea(): Area
    {
        return Area::create(
            BoundaryId::generate(),
            BoundaryName::fromString('Rio Primero Area'),
            new Polygon(array(array(
                array(-63.65, -31.31),
                array(-63.65, -31.36),
                array(-63.58, -31.36),
                array(-63.58, -31.31),
                array(-63.65, -31.31)
            )), 4326)
        );
    }
}
