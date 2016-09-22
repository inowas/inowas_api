<?php

namespace tests\AppBundle\Model;

use AppBundle\Exception\InvalidArgumentException;
use AppBundle\Model\LatLng;

class LatLngTest extends \PHPUnit_Framework_TestCase
{

    public function setUp(){

    }

    public function testCreateObjectFromObjectWithPropertiesLatAndLng(){
        $latLngObj = json_decode('{"lat": 2.1, "lng": 3.1}');
        $latLng = LatLng::fromObject($latLngObj);
        $this->assertInstanceOf(LatLng::class, $latLng);
    }

    public function testGetters(){
        $latLngObj = json_decode('{"lat": 2.1, "lng": 3.1}');
        $latLng = LatLng::fromObject($latLngObj);
        $this->assertEquals(3.1, $latLng->getLng());
        $this->assertEquals(2.1, $latLng->getLat());
    }

    public function testThrowsExceptionIfObjectHasNoPropertyLat(){
        $latLngObj = json_decode('{"l": 2.1, "lng": 3.1}');
        $this->setExpectedException(InvalidArgumentException::class);
        LatLng::fromObject($latLngObj);
    }

    public function testThrowsExceptionIfObjectHasNoPropertyLng(){
        $latLngObj = json_decode('{"lat": 2.1, "l": 3.1}');
        $this->setExpectedException(InvalidArgumentException::class);
        LatLng::fromObject($latLngObj);
    }
}
