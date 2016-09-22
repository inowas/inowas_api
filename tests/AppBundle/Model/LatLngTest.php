<?php

namespace tests\AppBundle\Model;

use AppBundle\Exception\InvalidArgumentException;
use AppBundle\Model\LatLng;

class LatLngTest extends \PHPUnit_Framework_TestCase
{

    public function setUp(){

    }

    public function testCreateObjectFromObjectWithPropertiesLatAndLng(){
        $json = '{"lat": 2.1, "lng": 3.1}';
        $latLng = LatLng::fromJson($json);
        $this->assertInstanceOf(LatLng::class, $latLng);
    }

    public function testGetters(){
        $json = '{"lat": 2.1, "lng": 3.1}';
        $latLng = LatLng::fromJson($json);
        $this->assertEquals(3.1, $latLng->getLng());
        $this->assertEquals(2.1, $latLng->getLat());
    }

    public function testThrowsExceptionIfJsonObjectHasNoPropertyLat(){
        $json = '{"l": 2.1, "lng": 3.1}';
        $this->setExpectedException(InvalidArgumentException::class);
        LatLng::fromJson($json);
    }

    public function testThrowsExceptionIfJsonObjectHasNoPropertyLng(){
        $json = '{"lat": 2.1, "l": 3.1}';
        $this->setExpectedException(InvalidArgumentException::class);
        LatLng::fromJson($json);
    }
}
