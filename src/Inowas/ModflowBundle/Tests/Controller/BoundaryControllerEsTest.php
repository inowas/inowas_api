<?php

namespace Inowas\ModflowBundle\Tests\Controller;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use FOS\UserBundle\Doctrine\UserManager;
use Inowas\AppBundle\Model\User;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Modflow\Model\Command\AddBoundary;
use Inowas\Modflow\Model\Command\AddModflowScenario;
use Inowas\Modflow\Model\Command\ChangeModflowModelDescription;
use Inowas\Modflow\Model\Command\ChangeModflowModelName;
use Inowas\Modflow\Model\Command\CreateModflowModel;
use Inowas\Modflow\Model\LayerNumber;
use Inowas\Common\Id\ModflowId;
use Inowas\Modflow\Model\SoilModelDescription;
use Inowas\Modflow\Model\SoilmodelName;
use Inowas\Common\Boundaries\PumpingRates;
use Inowas\Common\Id\UserId;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellType;
use Inowas\Modflow\Projection\BoundaryList\BoundaryFinder;
use Prooph\ServiceBus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BoundaryControllerEsTest extends WebTestCase
{

    /** @var  CommandBus $commandBus */
    private $commandBus;

    /** @var  BoundaryFinder */
    private $boundaryFinder;

    /** @var  UserManager */
    private $userManager;

    /** @var  User */
    private $user;

    /** @var  \Inowas\Common\Id\UserId */
    private $userId;

    /** @var  \Inowas\Common\Id\ModflowId */
    private $baseModelId;

    /** @var  \Inowas\Common\Id\ModflowId */
    private $scenarioId;

    /** @var  \Inowas\Common\Boundaries\WellBoundary */
    private $well1;

    /** @var  \Inowas\Common\Boundaries\WellBoundary */
    private $well2;

    /** @var  \Inowas\Common\Boundaries\WellBoundary */
    private $well3;

    /** @var  \Inowas\Common\Boundaries\WellBoundary */
    private $well4;

    public function setUp()
    {
        self::bootKernel();
        $this->commandBus = static::$kernel->getContainer()
            ->get('prooph_service_bus.modflow_command_bus')
        ;

        $this->userManager = static::$kernel->getContainer()
            ->get('fos_user.user_manager')
        ;

        $this->boundaryFinder = static::$kernel->getContainer()
            ->get('inowas.model_boundaries_finder')
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

        $this->baseModelId = ModflowId::generate();
        $this->scenarioId = ModflowId::generate();
        $this->userId = UserId::fromString($this->user->getId()->toString());
        $this->commandBus->dispatch(CreateModflowModel::byUserWithModelId($this->userId, $this->baseModelId));
        $this->commandBus->dispatch(ChangeModflowModelName::forModflowModel($this->userId, $this->baseModelId, SoilmodelName::fromString('TestName')));
        $this->commandBus->dispatch(ChangeModflowModelDescription::forModflowModel($this->userId, $this->baseModelId, SoilModelDescription::fromString('TestDescription')));

        $this->well1 = WellBoundary::createWithAllParams(
            BoundaryId::generate(),
            BoundaryName::fromString('Well1'),
            Geometry::fromPoint(new Point(1,2)),
            WellType::fromString(WellType::TYPE_INDUSTRIAL_WELL),
            LayerNumber::fromInteger(1),
            PumpingRates::create()
        );

        $this->well2 = WellBoundary::createWithAllParams(
            BoundaryId::generate(),
            BoundaryName::fromString('Well2'),
            Geometry::fromPoint(new Point(2,3)),
            WellType::fromString(WellType::TYPE_PUBLIC_WELL),
            LayerNumber::fromInteger(2),
            PumpingRates::create()
        );

        $this->well3 = WellBoundary::createWithAllParams(
            BoundaryId::generate(),
            BoundaryName::fromString('Well3'),
            Geometry::fromPoint(new Point(2,3)),
            WellType::fromString(WellType::TYPE_PUBLIC_WELL),
            LayerNumber::fromInteger(2),
            PumpingRates::create()
        );

        $this->well4 = WellBoundary::createWithAllParams(
            BoundaryId::generate(),
            BoundaryName::fromString('Well4'),
            Geometry::fromPoint(new Point(2,3)),
            WellType::fromString(WellType::TYPE_PUBLIC_WELL),
            LayerNumber::fromInteger(2),
            PumpingRates::create()
        );

        $this->commandBus->dispatch(AddBoundary::toBaseModel($this->userId, $this->baseModelId, $this->well1));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($this->userId, $this->baseModelId, $this->well2));

        $this->commandBus->dispatch(AddModflowScenario::from($this->userId, $this->baseModelId, $this->scenarioId));
        $this->commandBus->dispatch(ChangeModflowModelName::forScenario($this->userId, $this->baseModelId, $this->scenarioId, SoilmodelName::fromString('Scenario_1')));
        $this->commandBus->dispatch(ChangeModflowModelDescription::forScenario($this->userId, $this->baseModelId, $this->scenarioId, SoilModelDescription::fromString('Scenario_Description_1')));
        $this->commandBus->dispatch(AddBoundary::toScenario($this->userId, $this->baseModelId, $this->scenarioId, $this->well3));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($this->userId, $this->baseModelId, $this->well4));
    }

    /**
     * @test
     */
    public function it_loads_the_boundaries_from_the_projection()
    {
        $this->assertCount(2, $this->boundaryFinder->findByUserAndBaseModelId($this->userId, $this->baseModelId));
        $this->assertCount(2, $this->boundaryFinder->findByUserAndBaseModelAndScenarioId($this->userId, $this->baseModelId, $this->scenarioId));
    }

    /**
     * @test
     */
    public function it_loads_the_boundaries_from_the_basemodel_controller()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            sprintf('/api/modflow/model/%s.json', $this->baseModelId->toString()),
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->user->getApiKey())
        );

        $response = $client->getResponse()->getContent();
        $response = json_decode($response);
        $this->assertCount(3, $response);
        $well1 = $response[0];
        $this->assertEquals('Well1', $well1->name);
        $well2 = $response[1];
        $this->assertEquals('Well2', $well2->name);
        $well4 = $response[2];
        $this->assertEquals('Well4', $well4->name);
    }

    /**
     * @test
     */
    public function it_loads_the_boundaries_from_the_scenario_controller()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            sprintf('/api/modflow/model/%s/scenario/%s.json', $this->baseModelId->toString(), $this->scenarioId->toString()),
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->user->getApiKey())
        );

        $response = $client->getResponse()->getContent();
        $response = json_decode($response);
        $this->assertCount(3, $response);
        $well1 = $response[0];
        $this->assertEquals('Well1', $well1->name);
        $well2 = $response[1];
        $this->assertEquals('Well2', $well2->name);
        $well3 = $response[2];
        $this->assertEquals('Well3', $well3->name);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $user = $this->userManager->findUserByUsername($this->user->getUsername());
        $this->userManager->deleteUser($user);
    }
}
