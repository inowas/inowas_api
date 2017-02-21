<?php

namespace Inowas\ModflowBundle\Tests\Controller;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use FOS\UserBundle\Doctrine\UserManager;
use Inowas\AppBundle\Model\User;
use Inowas\Modflow\Model\BoundaryGeometry;
use Inowas\Modflow\Model\BoundaryId;
use Inowas\Modflow\Model\BoundaryName;
use Inowas\Modflow\Model\Command\AddBoundary;
use Inowas\Modflow\Model\Command\AddModflowScenario;
use Inowas\Modflow\Model\Command\ChangeModflowModelDescription;
use Inowas\Modflow\Model\Command\ChangeModflowModelName;
use Inowas\Modflow\Model\Command\CreateModflowModel;
use Inowas\Modflow\Model\LayerNumber;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\ModflowModelDescription;
use Inowas\Modflow\Model\ModflowModelName;
use Inowas\Modflow\Model\PumpingRates;
use Inowas\Modflow\Model\UserId;
use Inowas\Modflow\Model\WellBoundary;
use Inowas\Modflow\Model\WellType;
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

    /** @var  UserId */
    private $userId;

    /** @var  ModflowId */
    private $baseModelId;

    /** @var  ModflowId */
    private $scenarioId;

    /** @var  WellBoundary */
    private $well1;

    /** @var  WellBoundary */
    private $well2;

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
        $this->commandBus->dispatch(ChangeModflowModelName::forModflowModel($this->userId, $this->baseModelId, ModflowModelName::fromString('TestName')));
        $this->commandBus->dispatch(ChangeModflowModelDescription::forModflowModel($this->userId, $this->baseModelId, ModflowModelDescription::fromString('TestDescription')));

        $this->well1 = WellBoundary::createWithAllParams(
            BoundaryId::generate(),
            BoundaryName::fromString('Well1'),
            BoundaryGeometry::fromPoint(new Point(1,2)),
            WellType::fromString(WellType::TYPE_INDUSTRIAL_WELL),
            LayerNumber::fromInteger(1),
            PumpingRates::create()
        );

        $this->well2 = WellBoundary::createWithAllParams(
            BoundaryId::generate(),
            BoundaryName::fromString('Well2'),
            BoundaryGeometry::fromPoint(new Point(2,3)),
            WellType::fromString(WellType::TYPE_PUBLIC_WELL),
            LayerNumber::fromInteger(2),
            PumpingRates::create()
        );
        $this->commandBus->dispatch(AddBoundary::toBaseModel($this->userId, $this->baseModelId, $this->well1));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($this->userId, $this->baseModelId, $this->well2));

        $this->commandBus->dispatch(AddModflowScenario::from($this->userId, $this->baseModelId, $this->scenarioId));
        $this->commandBus->dispatch(ChangeModflowModelName::forScenario($this->userId, $this->baseModelId, $this->scenarioId, ModflowModelName::fromString('Scenario_1')));
        $this->commandBus->dispatch(ChangeModflowModelDescription::forScenario($this->userId, $this->baseModelId, $this->scenarioId, ModflowModelDescription::fromString('Scenario_Description_1')));
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
     * {@inheritDoc}
     */
    public function tearDown()
    {}
}
