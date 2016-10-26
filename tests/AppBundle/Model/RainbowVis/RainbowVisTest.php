<?php

namespace Tests\AppBundle\Model\RainbowVis;

use AppBundle\Model\RainbowVis\RainbowVis;

class RainbowVisTest extends \PHPUnit_Framework_TestCase
{
    /** @var  RainbowVis */
    protected $rainbowVis;

    public function setUp()
    {}

    public function testCanInstantiate(){
        $this->rainbowVis = new RainbowVis(array('red', 'blue'), 10, 20);
        $this->assertInstanceOf(RainbowVis::class, $this->rainbowVis);
    }

    public function testCanDoColorAtWithTwoColors(){
        $this->rainbowVis = new RainbowVis(array('black', 'white'), 10, 20);
        $this->assertEquals('262626', $this->rainbowVis->colorAt(15));
    }

    public function testCanDoColorAtWithThreeColors(){
        $this->rainbowVis = new RainbowVis(array('black', 'white', 'blue'), 0, 40);
        $this->assertEquals('ffffff', $this->rainbowVis->colorAt(20));
        $this->assertEquals('808080', $this->rainbowVis->colorAt(10));
        $this->assertEquals('8080ff', $this->rainbowVis->colorAt(30));
    }
}
