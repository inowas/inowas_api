<?php

namespace Inowas\ModflowBundle\Tests\Model;

use Inowas\ModflowBundle\Model\Boundary\ConstantHeadBoundary;
use Inowas\ModflowBundle\Model\Boundary\GeneralHeadBoundary;
use Inowas\ModflowBundle\Model\Boundary\RechargeBoundary;
use Inowas\ModflowBundle\Model\Boundary\RiverBoundary;
use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\BoundaryFactory;

class BoundaryFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateWithType(){
        $this->assertInstanceOf(ConstantHeadBoundary::class, BoundaryFactory::create('chd'));
        $this->assertInstanceOf(GeneralHeadBoundary::class, BoundaryFactory::create('ghb'));
        $this->assertInstanceOf(RechargeBoundary::class, BoundaryFactory::create('rch'));
        $this->assertInstanceOf(RiverBoundary::class, BoundaryFactory::create('riv'));
        $this->assertInstanceOf(WellBoundary::class, BoundaryFactory::create('wel'));
    }

    public function testCreateChd(){
        $this->assertInstanceOf(ConstantHeadBoundary::class, BoundaryFactory::createChd());
    }

    public function testCreateGhb(){
        $this->assertInstanceOf(GeneralHeadBoundary::class, BoundaryFactory::createGhb());
    }

    public function testCreateRch(){
        $this->assertInstanceOf(RechargeBoundary::class, BoundaryFactory::createRch());
    }

    public function testCreateRiv(){
        $this->assertInstanceOf(RiverBoundary::class, BoundaryFactory::createRiv());
    }

    public function testCreateWel(){
        $this->assertInstanceOf(WellBoundary::class, BoundaryFactory::createWel());
    }
}
