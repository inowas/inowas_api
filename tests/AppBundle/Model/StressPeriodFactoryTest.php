<?php

namespace Tests\AppBundle\Model;


use AppBundle\Model\StressPeriodFactory;

class StressPeriodFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Model\Stressperiod', StressPeriodFactory::create());
    }

}
