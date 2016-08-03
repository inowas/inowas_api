<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use AppBundle\Entity\UserProfile;
use AppBundle\Model\UserFactory;
use Ramsey\Uuid\Uuid;

class UserTest extends \PHPUnit_Framework_TestCase
{

    /** @var  User */
    protected $user;

    /**
     * {@inheritDoc}
     */
    public function setUp(){
        $this->user = UserFactory::create();
    }

    public function testInstantiate(){
        $this->assertInstanceOf(User::class, $this->user);
        $this->assertInstanceOf(Uuid::class, $this->user->getId());
        $this->assertInstanceOf(Uuid::class, $this->user->getApiKey());
        $this->assertInstanceOf(UserProfile::class, $this->user->getProfile());
    }

    public function testGenerateNewApiKey(){
        $this->assertInstanceOf(Uuid::class, $this->user->getApiKey());
        $apiKey = $this->user->getApiKey();
        $this->assertEquals($apiKey, $this->user->getApiKey());
        $this->user->generateNewApiKey();
        $this->assertInstanceOf(Uuid::class, $this->user->getApiKey());
        $this->assertNotEquals($apiKey, $this->user->getApiKey());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->user);
    }
}
