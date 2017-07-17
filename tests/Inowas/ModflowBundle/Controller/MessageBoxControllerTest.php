<?php

declare(strict_types=1);

namespace Tests\Inowas\ModflowBundle\Controller;

use FOS\UserBundle\Doctrine\UserManager;
use Inowas\AppBundle\Model\User;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Ramsey\Uuid\Uuid;
use Tests\Inowas\ModflowBundle\EventSourcingBaseTest;

class MessageBoxControllerTest extends EventSourcingBaseTest
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
    public function it_returns_422_with_empty_request(): void
    {
        $userId = UserId::fromString($this->user->getId()->toString());
        $apiKey = $this->user->getApiKey();
        $username = $this->user->getName();

        $modelId = ModflowId::generate();
        $this->createModelWithSoilmodel($userId, $modelId);

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
}
