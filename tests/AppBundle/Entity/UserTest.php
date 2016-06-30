<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use AppBundle\Model\UserFactory;

class UserTest extends \PHPUnit_Framework_TestCase
{

    /** @var  User */
    protected $user;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->user = UserFactory::create();
    }

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Entity\User', $this->user);
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $this->user->getId());
        $this->assertInstanceOf('AppBundle\Entity\UserProfile', $this->user->getProfile());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->user);
    }
}
