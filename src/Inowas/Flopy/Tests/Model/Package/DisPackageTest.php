<?php

namespace Inowas\Flopy\Tests\Model\Package;

use Inowas\Flopy\Model\Package\DisPackage;

class DisPackageTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiate() {
        $dis = new DisPackage();
        $this->assertInstanceOf(DisPackage::class, $dis);
    }

    public function testDisPackageHasJsonSerializeImplementedAndReturnsJson() {
        $dis = new DisPackage();
        $this->assertJson(json_encode($dis));
    }

    public function testDisPackageProperties() {
        $dis = new DisPackage();
        $json = json_encode($dis);
        $disObj = json_decode($json);
        $this->assertObjectHasAttribute('nlay', $disObj);
        $this->assertObjectHasAttribute('nrow', $disObj);
        $this->assertObjectHasAttribute('ncol', $disObj);
        $this->assertObjectHasAttribute('nper', $disObj);
        $this->assertObjectHasAttribute('delr', $disObj);
        $this->assertObjectHasAttribute('delc', $disObj);
        $this->assertObjectHasAttribute('laycbd', $disObj);
        $this->assertObjectHasAttribute('top', $disObj);
        $this->assertObjectHasAttribute('botm', $disObj);
        $this->assertObjectHasAttribute('perlen', $disObj);
        $this->assertObjectHasAttribute('nstp', $disObj);
        $this->assertObjectHasAttribute('tsmult', $disObj);
        $this->assertObjectHasAttribute('steady', $disObj);
        $this->assertObjectHasAttribute('itmuni', $disObj);
        $this->assertObjectHasAttribute('lenuni', $disObj);
        $this->assertObjectHasAttribute('extension', $disObj);
        $this->assertObjectHasAttribute('unitnumber', $disObj);
        $this->assertObjectHasAttribute('xul', $disObj);
        $this->assertObjectHasAttribute('yul', $disObj);
        $this->assertObjectHasAttribute('rotation', $disObj);
        $this->assertObjectHasAttribute('proj4_str', $disObj);
        $this->assertObjectHasAttribute('start_datetime', $disObj);
    }
}
