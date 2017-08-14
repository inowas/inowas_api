<?php

declare(strict_types=1);

namespace Tests\Inowas\ModflowBundle\Controller;

use FOS\UserBundle\Doctrine\UserManager;
use Inowas\AppBundle\Model\User;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\ModflowModel;
use Inowas\ModflowModel\Model\Command\AddBoundary;
use Tests\Inowas\ModflowBundle\EventSourcingBaseTest;

class MessageBoxControllerTest extends EventSourcingBaseTest
{
    /** @var UserManager */
    protected $userManager;

    /** @var User */
    protected $user;

    private $fileLocation = 'spec/example/modflow/command/';

    public function setUp(): void
    {
        parent::setUp();

        $this->userManager = $this->container->get('fos_user.user_manager');

        $this->commandBus = static::$kernel->getContainer()
            ->get('prooph_service_bus.modflow_command_bus');

        $user = $this->userManager->findUserByUsername('testUser');

        if(! $user instanceof User){

            /** @var User $user */
            $user = $this->userManager->createUser();
            $user->setUsername('testUser');
            $user->setName('testUserName');
            $user->setEmail('testUser@testUser.com');
            $user->setPlainPassword('testUserPassword');
            $user->setEnabled(true);
            $this->userManager->updateUser($user);
        }

        $this->user = $user;
    }

    /**
     * @test
     */
    public function it_returns_422_with_empty_request(): void
    {
        $apiKey = $this->user->getApiKey();
        $client = static::createClient();
        $client->request(
            'POST',
            '/v2/messagebox',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $apiKey)
        );

        $response = $client->getResponse();
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_receives_a_create_model_command_and_creates_model(): void
    {
        $command = json_decode(file_get_contents($this->fileLocation.'createModflowModel.json'), true);
        $payload = $command['payload'];

        $modelId = ModflowId::fromString($command['payload']['id']);

        $apiKey = $this->user->getApiKey();
        $client = static::createClient();
        $client->request(
            'POST',
            '/v2/messagebox',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $apiKey),
            json_encode($command)
        );

        $response = $client->getResponse();
        $this->assertEquals(202, $response->getStatusCode());

        $model = $this->container->get('inowas.modflowmodel.manager')->findModel($modelId);
        $this->assertInstanceOf(ModflowModel::class, $model);
        $this->assertEquals($payload['id'], $model->id()->toString());
        $this->assertEquals($payload['name'], $model->name()->toString());
        $this->assertEquals($payload['description'], $model->description()->toString());
        $this->assertEquals($payload['geometry']['coordinates'], $model->geometry()->toArray());
        $this->assertEquals($payload['bounding_box'], $model->boundingBox()->toArray());
        $this->assertEquals($payload['grid_size'], $model->gridSize()->toArray());
        $this->assertEquals($payload['time_unit'], $model->timeUnit()->toInt());
        $this->assertEquals($payload['length_unit'], $model->lengthUnit()->toInt());
        $this->assertInstanceOf(ActiveCells::class, $model->activeCells());
    }

    /**
     * @test
     */
    public function it_can_receive_update_model_command(): void
    {

        $userId = UserId::fromString($this->user->getId()->toString());
        $modelId = ModflowId::fromString('f3f6788a-61a6-410e-a14d-af7ecca6babb');
        $this->createModel($userId, $modelId);

        $command = json_decode(file_get_contents($this->fileLocation . 'updateModflowModel.json'), true);
        $payload = $command['payload'];

        $apiKey = $this->user->getApiKey();
        $client = static::createClient();
        $client->request(
            'POST',
            '/v2/messagebox',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $apiKey),
            json_encode($command)
        );

        $response = $client->getResponse();
        $this->assertEquals(202, $response->getStatusCode());

        $modelId = ModflowId::fromString($command['payload']['id']);
        $model = $this->container->get('inowas.modflowmodel.manager')->findModel($modelId);

        $this->assertInstanceOf(ModflowModel::class, $model);
        $this->assertEquals($payload['id'], $model->id()->toString());
        $this->assertEquals($payload['name'], $model->name()->toString());
        $this->assertEquals($payload['description'], $model->description()->toString());
        $this->assertEquals($payload['geometry']['coordinates'], $model->geometry()->toArray());
        $this->assertEquals($payload['bounding_box'], $model->boundingBox()->toArray());
        $this->assertEquals($payload['grid_size'], $model->gridSize()->toArray());
        $this->assertEquals($payload['time_unit'], $model->timeUnit()->toInt());
        $this->assertEquals($payload['length_unit'], $model->lengthUnit()->toInt());
        $this->assertInstanceOf(ActiveCells::class, $model->activeCells());
    }

    /**
     * @test
     */
    public function it_can_receive_delete_model_command(): void
    {
        $userId = UserId::fromString($this->user->getId()->toString());
        $modelId = ModflowId::fromString('f3f6788a-61a6-410e-a14d-af7ecca6babb');
        $this->createModel($userId, $modelId);

        $command = json_decode(file_get_contents($this->fileLocation . 'deleteModflowModel.json'), true);

        $apiKey = $this->user->getApiKey();
        $client = static::createClient();
        $client->request(
            'POST',
            '/v2/messagebox',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $apiKey),
            json_encode($command)
        );

        $this->assertFalse($this->container->get('inowas.modflowmodel.model_finder')->modelExists($modelId));
    }

    /**
     * @test
     */
    public function it_can_receive_update_stress_periods_command(): void
    {
        $userId = UserId::fromString($this->user->getId()->toString());
        $modelId = ModflowId::fromString('f3f6788a-61a6-410e-a14d-af7ecca6babb');
        $this->createModel($userId, $modelId);

        $command = json_decode(file_get_contents($this->fileLocation . 'updateStressPeriods.json'), true);

        $apiKey = $this->user->getApiKey();
        $client = static::createClient();
        $client->request(
            'POST',
            '/v2/messagebox',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $apiKey),
            json_encode($command)
        );

        $response = $client->getResponse();
        $this->assertEquals(202, $response->getStatusCode());

    }


    /**
     * @test
     */
    public function it_can_receive_add_chd_boundary_command(): void
    {
        $userId = UserId::fromString($this->user->getId()->toString());
        $modelId = ModflowId::fromString('f3f6788a-61a6-410e-a14d-af7ecca6babb');
        $this->createModel($userId, $modelId);

        $command = json_decode(file_get_contents($this->fileLocation . 'addChdBoundary.json'), true);
        $command['payload']['id'] = $modelId->toString();

        $apiKey = $this->user->getApiKey();
        $client = static::createClient();
        $client->request(
            'POST',
            '/v2/messagebox',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $apiKey),
            json_encode($command)
        );

        $response = $client->getResponse();
        $this->assertEquals(202, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_can_receive_remove_boundary_command(): void
    {
        $userId = UserId::fromString($this->user->getId()->toString());
        $modelId = ModflowId::fromString('f3f6788a-61a6-410e-a14d-af7ecca6babb');
        $this->createModel($userId, $modelId);

        $well = $this->createWellBoundary();
        $this->commandBus->dispatch(AddBoundary::forModflowModel($userId, $modelId, $well));

        $command = json_decode(file_get_contents($this->fileLocation . 'removeBoundary.json'), true);

        $command['payload']['id'] = $modelId->toString();
        $command['payload']['boundary_id'] = $well->boundaryId()->toString();

        $apiKey = $this->user->getApiKey();
        $client = static::createClient();
        $client->request(
            'POST',
            '/v2/messagebox',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $apiKey),
            json_encode($command)
        );

        $response = $client->getResponse();
        $this->assertEquals(202, $response->getStatusCode());
    }
}
