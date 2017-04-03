<?php

namespace Tests\Inowas\Modflow\Model\Packages;

use Inowas\Common\Modflow\Mxiter;
use Inowas\Modflow\Model\Packages\PcgPackage;

class PcgPackageTest extends \PHPUnit_Framework_TestCase
{
    public function test_create_from_default(): void
    {
        $package = PcgPackage::fromDefaults();
        $this->assertInstanceOf(PcgPackage::class, $package);
        $json = json_encode($package);
        $this->assertJson($json);
    }

    public function test_update_mxiter(): void
    {
        $package = PcgPackage::fromDefaults();
        $package = $package->updateMxiter(Mxiter::fromInteger(4));
        $obj = \json_decode(json_encode($package));
        $this->assertEquals(4, $obj->mxiter);
    }
}
