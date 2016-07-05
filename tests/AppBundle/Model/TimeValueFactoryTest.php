<?php

namespace Tests\AppBundle\Model;


use AppBundle\Model\TimeValueFactory;

class TimeValueFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Model\TimeValue', TimeValueFactory::create());
    }

}
