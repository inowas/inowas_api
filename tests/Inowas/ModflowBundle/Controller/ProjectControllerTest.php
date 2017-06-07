<?php

declare(strict_types=1);

namespace Tests\Inowas\ModflowBundle\Controller;

use FOS\UserBundle\Doctrine\UserManager;
use Inowas\AppBundle\Model\User;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\ScenarioAnalysis\Model\Command\CreateScenarioAnalysis;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisDescription;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisName;
use Tests\Inowas\ModflowBundle\EventSourcingBaseTest;

class ProjectControllerTest extends EventSourcingBaseTest
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
    public function it_returns_the_project_list_from_the_user(): void
    {
        $userId = UserId::fromString($this->user->getId()->toString());
        $apiKey = $this->user->getApiKey();
        $username = $this->user->getName();

        $modelId = ModflowId::generate();
        $this->createModelWithName($userId, $modelId);

        $scenarioAnalysisId = ScenarioAnalysisId::generate();
        $calculationId = ModflowId::generate();
        $this->commandBus->dispatch(CreateScenarioAnalysis::byUserWithBaseModelNameAndDescription(
            $scenarioAnalysisId,
            $userId,
            $modelId,
            $calculationId,
            ScenarioAnalysisName::fromString('TestName'),
            ScenarioAnalysisDescription::fromString('TestDescription')
        ));

        $client = static::createClient();
        $client->request(
            'GET',
            '/v2/projects',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $apiKey)
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($body));
        $this->assertCount(1, $body);
        $saDetails = $body[0];

        $this->assertTrue(array_key_exists('id', $saDetails));
        $this->assertEquals($scenarioAnalysisId->toString(), $saDetails['id']);
        $this->assertTrue(array_key_exists('name', $saDetails));
        $this->assertEquals('TestName', $saDetails['name']);
        $this->assertTrue(array_key_exists('description', $saDetails));
        $this->assertEquals('TestDescription', $saDetails['description']);
        $this->assertTrue(array_key_exists('project', $saDetails));
        $this->assertTrue(array_key_exists('application', $saDetails));
        $this->assertTrue(array_key_exists('user_id', $saDetails));
        $this->assertEquals($userId->toString(), $saDetails['user_id']);
        $this->assertTrue(array_key_exists('user_name', $saDetails));
        $this->assertEquals($username, $saDetails['user_name']);
        $this->assertTrue(array_key_exists('created_at', $saDetails));
        $this->assertTrue(array_key_exists('public', $saDetails));
    }

    /**
     * @test
     */
    public function it_returns_all_public_scenario_analyses(): void
    {
        $userId = UserId::fromString($this->user->getId()->toString());
        $apiKey = $this->user->getApiKey();
        $username = $this->user->getName();

        $modelId = ModflowId::generate();
        $this->createModelWithName($userId, $modelId);

        $scenarioAnalysisId = ScenarioAnalysisId::generate();
        $calculationId = ModflowId::generate();
        $this->commandBus->dispatch(CreateScenarioAnalysis::byUserWithBaseModelNameAndDescription(
            $scenarioAnalysisId,
            $userId,
            $modelId,
            $calculationId,
            ScenarioAnalysisName::fromString('TestName'),
            ScenarioAnalysisDescription::fromString('TestDescription')
        ));

        $client = static::createClient();
        $client->request(
            'GET',
            '/v2/projects/public',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $apiKey)
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($body));
        $this->assertCount(1, $body);
        $saDetails = $body[0];

        $this->assertTrue(array_key_exists('id', $saDetails));
        $this->assertEquals($scenarioAnalysisId->toString(), $saDetails['id']);
        $this->assertTrue(array_key_exists('name', $saDetails));
        $this->assertEquals('TestName', $saDetails['name']);
        $this->assertTrue(array_key_exists('description', $saDetails));
        $this->assertEquals('TestDescription', $saDetails['description']);
        $this->assertTrue(array_key_exists('project', $saDetails));
        $this->assertTrue(array_key_exists('application', $saDetails));
        $this->assertTrue(array_key_exists('user_id', $saDetails));
        $this->assertEquals($userId->toString(), $saDetails['user_id']);
        $this->assertTrue(array_key_exists('user_name', $saDetails));
        $this->assertEquals($username, $saDetails['user_name']);
        $this->assertTrue(array_key_exists('created_at', $saDetails));
        $this->assertTrue(array_key_exists('public', $saDetails));
    }
}
