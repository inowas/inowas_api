<?php

declare(strict_types=1);

namespace Tests\Inowas\ModflowBundle\Controller;

use FOS\UserBundle\Doctrine\UserManager;
use Inowas\AppBundle\Model\User;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\ModflowCalculation\Model\Command\CreateModflowModelCalculation;
use Ramsey\Uuid\Uuid;
use Tests\Inowas\ModflowBundle\EventSourcingBaseTest;

class ModflowCalculationControllerTest extends EventSourcingBaseTest
{
    /** @var UserManager */
    protected $userManager;

    /** @var User */
    protected $user;

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
    public function it_creates_a_new_calculation_with_a_given_modflow_model(): void
    {
        $userId = UserId::fromString($this->user->getId()->toString());
        $apiKey = $this->user->getApiKey();
        $modflowId = ModflowId::generate();
        $this->createModelWithSoilmodel($userId, $modflowId);

        $body = new \stdClass;
        $body->model_id = $modflowId->toString();
        $body->start_date_time = DateTime::fromDateTime(new \DateTime('2005-01-01'))->toAtom();
        $body->end_date_time = DateTime::fromDateTime(new \DateTime('2007-12-31'))->toAtom();

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/calculations',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $apiKey),
            json_encode($body)
        );

        $response = $client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());
        $client->followRedirect();
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $calculations = $this->container->get('inowas.modflowcalculation.calculation_list_finder')->findCalculationsByModelId($modflowId);
        $this->assertCount(1, $calculations);
    }

    /**
     * @test
     */
    public function it_returns_calculation_details_by_calculation_id(): void
    {
        $userId = UserId::fromString($this->user->getId()->toString());
        $apiKey = $this->user->getApiKey();
        $modflowId = ModflowId::generate();
        $this->createModelWithSoilmodel($userId, $modflowId);

        $calculationId = ModflowId::generate();
        $startDateTime = DateTime::fromDateTime(new \DateTime('2005-01-01'));
        $endDateTime = DateTime::fromDateTime(new \DateTime('2007-12-31'));
        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId($calculationId, $userId, $modflowId, $startDateTime, $endDateTime));

        $client = static::createClient();
        $client->request(
            'GET',
            sprintf('/api/calculations/%s', $calculationId->toString()),
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $apiKey)
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $response = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals($calculationId->toString(), $response['id']);
        $this->assertArrayHasKey('model_id', $response);
        $this->assertEquals($modflowId->toString(), $response['model_id']);
        $this->assertArrayHasKey('soilmodel_id', $response);
        $this->assertArrayHasKey('user_id', $response);
        $this->assertEquals($userId->toString(), $response['user_id']);
        $this->assertArrayHasKey('state', $response);
        $this->assertEquals(0, $response['state']);
        $this->assertArrayHasKey('start_date_time', $response);
        $this->assertEquals($startDateTime, DateTime::fromAtom($response['start_date_time']));
        $this->assertArrayHasKey('end_date_time', $response);
        $this->assertEquals($endDateTime, DateTime::fromAtom($response['end_date_time']));
    }

    /**
     * @test
     * @group messaging-integration-tests
     */
    public function it_calculates_calculation_id(): void
    {
        $userId = UserId::fromString($this->user->getId()->toString());
        $apiKey = $this->user->getApiKey();
        $modflowId = ModflowId::generate();
        $this->createModelWithSoilmodel($userId, $modflowId);

        $calculationId = ModflowId::generate();
        $startDateTime = DateTime::fromDateTime(new \DateTime('2005-01-01'));
        $endDateTime = DateTime::fromDateTime(new \DateTime('2007-12-31'));
        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId($calculationId, $userId, $modflowId, $startDateTime, $endDateTime));

        $client = static::createClient();
        $client->request(
            'POST',
            sprintf('/api/calculations/%s/calculate', $calculationId->toString()),
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $apiKey)
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_403_unauthorized_when_api_key_not_known(): void
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => Uuid::uuid4()->toString())
        );

        $response = $client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals('Username could not be found.', json_decode($response->getContent())->message);
    }

    /**
     * @test
     */
    public function it_returns_the_model_list(): void
    {
        $userId = UserId::fromString($this->user->getId()->toString());
        $apiKey = $this->user->getApiKey();
        $username = $this->user->getName();

        $modelId = ModflowId::generate();
        $this->createModelWithName($userId, $modelId);

        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $apiKey)
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($body));
        $this->assertCount(1, $body);
        $modelDetails = $body[0];
        $this->assertTrue(array_key_exists('id', $modelDetails));
        $this->assertEquals($modelId->toString(), $modelDetails['id']);
        $this->assertTrue(array_key_exists('user_id', $modelDetails));
        $this->assertEquals($userId->toString(), $modelDetails['user_id']);
        $this->assertTrue(array_key_exists('user_name', $modelDetails));
        $this->assertEquals($username, $modelDetails['user_name']);
    }

    /**
     * @test
     */
    public function it_returns_the_public_model_list(): void
    {
        $userId = UserId::fromString($this->user->getId()->toString());
        $apiKey = $this->user->getApiKey();
        $username = $this->user->getName();

        $modelId = ModflowId::generate();
        $this->createModelWithName($userId, $modelId);

        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modflowmodels/public',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $apiKey)
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($body));
        $this->assertCount(1, $body);
        $modelDetails = $body[0];
        $this->assertTrue(array_key_exists('id', $modelDetails));
        $this->assertEquals($modelId->toString(), $modelDetails['id']);
        $this->assertTrue(array_key_exists('user_id', $modelDetails));
        $this->assertEquals($userId->toString(), $modelDetails['user_id']);
        $this->assertTrue(array_key_exists('user_name', $modelDetails));
        $this->assertEquals($username, $modelDetails['user_name']);
    }

    /**
     * @test
     */
    public function it_creates_a_new_model(): void
    {
        $apiKey = $this->user->getApiKey();
        $userId = UserId::fromString($this->user->getId()->toString());
        $username = $this->user->getName();

        $body = new \stdClass();
        $body->name = "Hanoi 2005-2007";
        $body->description = "ModflowModel of Hanoi Area 2005-2007";
        $body->area_geometry = new \stdClass();
        $body->area_geometry->type = "polygon";
        $body->area_geometry->coordinates = [[[12.1, 10.2], [12.2, 10.2], [12.2, 10.1], [12.1, 10.1], [12.1, 10.2]]];
        $body->grid_size = new \stdClass();
        $body->grid_size->n_x = 100;
        $body->grid_size->n_y = 120;
        $body->time_unit = 4;
        $body->length_unit = 2;

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/modflowmodels',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'HTTP_X-AUTH-TOKEN' => $apiKey),
            json_encode($body)
        );

        $response = $client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());
        $client->followRedirect();
        $response = $client->getResponse();
        $modelDetails = json_decode($response->getContent(), true);

        $this->assertTrue(array_key_exists('id', $modelDetails));
        $this->assertTrue(array_key_exists('user_id', $modelDetails));
        $this->assertEquals($userId->toString(), $modelDetails['user_id']);
        $this->assertTrue(array_key_exists('user_name', $modelDetails));
        $this->assertEquals($username, $modelDetails['user_name']);
    }

    /**
     * @test
     */
    public function it_throws_invalid_input_creating_a_new_model_without_name(): void
    {
        $apiKey = $this->user->getApiKey();
        $body = new \stdClass();
        $body->description = "ModflowModel of Hanoi Area 2005-2007";
        $body->area_geometry = new \stdClass();
        $body->area_geometry->type = "polygon";
        $body->area_geometry->coordinates = [[[12.1, 10.2], [12.2, 10.2], [12.2, 10.1], [12.1, 10.1], [12.1, 10.2]]];
        $body->grid_size = new \stdClass();
        $body->grid_size->n_x = 100;
        $body->grid_size->n_y = 120;
        $body->time_unit = 4;
        $body->length_unit = 2;

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/modflowmodels',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'HTTP_X-AUTH-TOKEN' => $apiKey),
            json_encode($body)
        );

        $response = $client->getResponse();
        $this->assertEquals(422, $response->getStatusCode());
    }
}
