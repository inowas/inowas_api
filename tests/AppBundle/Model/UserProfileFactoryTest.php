<?php

namespace Tests\AppBundle\Model;


use AppBundle\Model\UserProfileFactory;

class UserProfileFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Entity\UserProfile', UserProfileFactory::create());
    }

}
