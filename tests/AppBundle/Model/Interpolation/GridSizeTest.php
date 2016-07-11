<?php

namespace Tests\AppBundle\Model\Interpolation;

use AppBundle\Model\Interpolation\GridSize;

class GridSizeTest extends \PHPUnit_Framework_TestCase
{

    public function testGridsizeHasPropertiesForXAndY()
    {
        $gridSize = new GridSize();
        $this->assertObjectHasAttribute('nX', $gridSize);
        $this->assertObjectHasAttribute('nY', $gridSize);
    }
    
    public function testGridsizeHasDefault0()
    {
        $gridSize = new GridSize();
        $this->assertEquals($gridSize->getNX(), 0);
        $this->assertEquals($gridSize->getNY(), 0);
    }

    public function testGridsizeHasDefaultContructor()
    {
        $gridSize = new GridSize(1,2);
        $this->assertEquals($gridSize->getNX(), 1);
        $this->assertEquals($gridSize->getNY(), 2);
    }

    public function testGridsizeSetter()
    {
        $gridSize = new GridSize();
        $gridSize->setNX(3);
        $gridSize->setNY(4);
        $this->assertEquals($gridSize->getNX(), 3);
        $this->assertEquals($gridSize->getNY(), 4);
    }
}
