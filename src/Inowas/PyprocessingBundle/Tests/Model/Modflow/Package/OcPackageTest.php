<?php

namespace Inowas\PyprocessingBundle\Tests\Model\Modflow\Package;

use Inowas\PyprocessingBundle\Model\Modflow\Package\OcPackage;

class OcPackageTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate() {
        $oc = new OcPackage();
        $this->assertInstanceOf(OcPackage::class, $oc);
    }

    public function testDisPackageHasJsonSerializeImplementedAndReturnsJson() {
        $oc = new OcPackage();
        $this->assertJson(json_encode($oc));
    }

    public function testDisPackageProperties() {
        $oc = new OcPackage();
        $json = json_encode($oc);
        $ocObj = json_decode($json);
        $this->assertObjectHasAttribute('ihedfm', $ocObj);
        $this->assertObjectHasAttribute('iddnfm', $ocObj);
        $this->assertObjectHasAttribute('chedfm', $ocObj);
        $this->assertObjectHasAttribute('cddnfm', $ocObj);
        $this->assertObjectHasAttribute('cboufm', $ocObj);
        $this->assertObjectHasAttribute('compact', $ocObj);
        $this->assertObjectHasAttribute('extension', $ocObj);
        $this->assertObjectHasAttribute('unitnumber', $ocObj);
    }
}
