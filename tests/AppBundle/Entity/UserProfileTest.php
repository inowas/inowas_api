<?php

namespace Tests\AppBundle\Entity;


use AppBundle\Entity\UserProfile;

class UserProfileTest extends \PHPUnit_Framework_TestCase
{

    /** @var  UserProfile */
    protected $userProfile;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->userProfile = new UserProfile();
    }

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Entity\UserProfile', $this->userProfile);
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $this->userProfile->getId());
    }

    public function testSetGetFirstName(){
        $this->userProfile->setFirstName(null);
        $this->assertEquals("", $this->userProfile->getFirstName());
        $firstName = "firstName";
        $this->userProfile->setFirstName($firstName);
        $this->assertEquals($firstName, $this->userProfile->getFirstName());
    }

    public function testSetGetLastName(){
        $this->userProfile->setLastName(null);
        $this->assertEquals("", $this->userProfile->getLastName());
        $lastName = "lastName";
        $this->userProfile->setLastName($lastName);
        $this->assertEquals($lastName, $this->userProfile->getLastName());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->user);
    }
}
