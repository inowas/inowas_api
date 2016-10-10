<?php

namespace Tests\AppBundle\Model;


use AppBundle\Model\UserFactory;

class UserFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Entity\User', UserFactory::create());
    }

    public function testCreateTestUser(){
        $user = UserFactory::createTestUser('testCase');
        $this->assertInstanceOf('AppBundle\Entity\User', $user);
        $this->assertContains('testCase', $user->getUsername());
        $this->assertContains('testCase', $user->getEmail());
        $this->assertContains('@', $user->getEmail());
        $this->assertTrue($user->isEnabled());
    }


}
