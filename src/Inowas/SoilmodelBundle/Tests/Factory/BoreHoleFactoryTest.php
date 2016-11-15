<?php

namespace Inowas\Soilmodel\Tests\Factory;

use Inowas\Soilmodel\Model\BoreHole;
use Inowas\Soilmodel\Factory\BoreHoleFactory;

class BoreHoleFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate(){
        $this->assertInstanceOf(BoreHole::class, BoreHoleFactory::create());
    }
}
