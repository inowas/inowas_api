<?php

namespace Inowas\SoilmodelBundle\Tests\Model;


use Inowas\Soilmodel\Model\BoreHole;
use Inowas\SoilmodelBundle\Model\BoreHoleFactory;

class BoreHoleFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate(){
        $this->assertInstanceOf(BoreHole::class, BoreHoleFactory::create());
    }
}
