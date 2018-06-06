<?php

namespace Tests\Inowas\Modflow\Model\Packages;

use Inowas\ModflowModel\Model\Packages\LpfPackage;

class LpfPackageTest extends \PHPUnit_Framework_TestCase
{
    public function test_create_from_default(): void
    {
        $package = LpfPackage::fromDefaults();
        $this->assertInstanceOf(LpfPackage::class, $package);
        $json = json_encode($package);
        $this->assertJson($json);
    }
}
