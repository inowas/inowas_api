<?php

namespace Inowas\Soilmodel\Tests\Factory;

use Inowas\SoilmodelBundle\Model\BoreHole;
use Inowas\SoilmodelBundle\Factory\BoreHoleFactory;

class BoreHoleFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate(){
        $this->assertInstanceOf(BoreHole::class, BoreHoleFactory::create());
    }
}
