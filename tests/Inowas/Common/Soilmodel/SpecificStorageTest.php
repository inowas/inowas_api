<?php

namespace Tests\Inowas\Common\Soilmodel;

use Inowas\Common\Soilmodel\SpecificStorage;
use Inowas\Common\Soilmodel\GeologicalLayerNumber;

class SpecificStorageTest extends \PHPUnit_Framework_TestCase
{

    public function test_store_a_simple_value(){
        $specificStorage = SpecificStorage::fromPointValue(1.01);
        $this->assertInstanceOf(SpecificStorage::class, $specificStorage);
        $this->assertEquals(1.01, $specificStorage->toValue());
        $this->assertEquals(false, $specificStorage->isLayerValue());
    }

    public function test_store_an_array_value_without_layer_information(){
        $arr = [[1,2,3],[2,4,6]];
        $specificStorage = SpecificStorage::fromLayerValue($arr);
        $this->assertInstanceOf(SpecificStorage::class, $specificStorage);
        $this->assertEquals($arr, $specificStorage->toValue());
        $this->assertEquals(true, $specificStorage->isLayerValue());
    }
}
