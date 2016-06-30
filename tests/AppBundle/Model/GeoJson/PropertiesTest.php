<?php

namespace Tests\AppBundle\Model\GeoJson;

use AppBundle\Model\GeoJson\Properties;

class PropertiesTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate(){
        $properties = new Properties();
        $properties->col = 3;
        $properties->row = 2;
        $this->assertInstanceOf('AppBundle\Model\GeoJson\Properties', $properties);
    }

}
