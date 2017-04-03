<?php

namespace Tests\Inowas\Modflow\Model\Packages;

use Inowas\Common\Modflow\Ihedfm;
use Inowas\Modflow\Model\Packages\OcPackage;

class OcPackageTest extends \PHPUnit_Framework_TestCase
{

    public function test_create_with_default_values(): void
    {
        $ocPackage = OcPackage::fromDefaults();
        $this->assertInstanceOf(OcPackage::class, $ocPackage);
    }

    public function test_update_ihedfm_values(): void
    {
        $ocPackage = OcPackage::fromDefaults();
        $ocPackage = $ocPackage->updateIhedfm(Ihedfm::fromInteger(5));
        $obj = json_decode(json_encode($ocPackage));
        $this->assertEquals(5, $obj->ihedfm);
    }
}
