<?php

namespace Inowas\Flopy\Tests\Model\Package;

use Inowas\Flopy\Model\Package\PcgPackage;

class PcgPackageTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function testInstantiate() {
        $pcg = new PcgPackage();
        $this->assertInstanceOf(PcgPackage::class, $pcg);
    }

    public function testDisPackageHasJsonSerializeImplementedAndReturnsJson() {
        $pcg = new PcgPackage();
        $this->assertJson(json_encode($pcg));
    }

    public function testDisPackageProperties() {
        $pcg = new PcgPackage();
        $json = json_encode($pcg);
        $pcgObj = json_decode($json);
        $this->assertObjectHasAttribute('mxiter', $pcgObj);
        $this->assertObjectHasAttribute('iter1', $pcgObj);
        $this->assertObjectHasAttribute('npcond', $pcgObj);
        $this->assertObjectHasAttribute('hclose', $pcgObj);
        $this->assertObjectHasAttribute('rclose', $pcgObj);
        $this->assertObjectHasAttribute('relax', $pcgObj);
        $this->assertObjectHasAttribute('nbpol', $pcgObj);
        $this->assertObjectHasAttribute('iprpcg', $pcgObj);
        $this->assertObjectHasAttribute('mutpcg', $pcgObj);
        $this->assertObjectHasAttribute('damp', $pcgObj);
        $this->assertObjectHasAttribute('dampt', $pcgObj);
        $this->assertObjectHasAttribute('ihcofadd', $pcgObj);
        $this->assertObjectHasAttribute('extension', $pcgObj);
        $this->assertObjectHasAttribute('unitnumber', $pcgObj);
    }
}