<?php

namespace Tests\Inowas\AppBundle\Tests\Controller;

use FOS\UserBundle\Doctrine\UserManager;
use Inowas\AppBundle\Model\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{

    /** @var  User */
    protected $user;

    /** @var  UserManager */
    protected $userManager;

    public function setUp(){

        self::bootKernel();

        $this->userManager = static::$kernel->getContainer()
            ->get('fos_user.user_manager')
        ;

        $username = 'testUser';
        $email = 'testUser@email';
        $password = 'testUserPassword';

        $this->user = $this->userManager->findUserByUsername($username);
        if (! $this->user instanceof User){
            $this->user = $this->userManager->createUser()
                ->setUsername($username)
                ->setEmail($email)
                ->setPlainPassword($password)
                ->setEnabled(true);

            $this->userManager->updateUser($this->user);
        }
    }

    public function testRetrieveAPIKeyWithCorrectCredentials(){

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/users/credentials.json',
            array(
                'username' => $this->user->getUsername(),
                'password' => 'testUserPassword'
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $response = $client->getResponse()->getContent();
        $this->assertJson($response);
        $response = json_decode($response);
        $this->assertEquals($this->user->getApiKey(), $response->api_key);
    }

    public function testRetrieveAPIKeyWithInCorrectCredentialsReturns401(){

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/users/credentials.json',
            array(
                'username' => $this->user->getUsername(),
                'password' => 'sadkjfh'
            )
        );

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }
}
