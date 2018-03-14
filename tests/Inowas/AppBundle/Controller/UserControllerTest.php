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

    public function testRetrieveAPIKeyWithCorrectCredentials(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/v2/users/credentials.json',
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

    public function testRetrieveAPIKeyWithInCorrectCredentialsReturns401(): void
    {

        $client = static::createClient();
        $client->request(
            'POST',
            '/v2/users/credentials.json',
            array(
                'username' => $this->user->getUsername(),
                'password' => 'sadkjfh'
            )
        );

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function it_gets_user_data(): void
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/v2/users.json',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $this->user->getApiKey()]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $response = $client->getResponse()->getContent();
        $this->assertJson($response);

        $arr = json_decode($response, true);
        $this->assertInternalType('array', $arr);
    }

    /**
     * @test
     */
    public function it_puts_user_data(): void
    {
        $client = static::createClient();
        $client->request(
            'PUT',
            '/v2/users.json',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $this->user->getApiKey()],
            json_encode([
                'firstName' => 'FN',
                'lastName' => 'LN',
                'email' => 'email@email.com',
                'institution' => 'inst'
            ])
        );

        $this->assertEquals(303, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $response = $client->getResponse()->getContent();
        $this->assertJson($response);

        $arr = json_decode($response, true);
        $this->assertInternalType('array', $arr);
        $this->assertArrayHasKey('profile', $arr);
    }
}
