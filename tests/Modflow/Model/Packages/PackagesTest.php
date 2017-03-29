<?php

namespace Tests\Inowas\Modflow\Model\Packages;

use Inowas\Modflow\Model\Packages\Packages;

class PackagesTest extends \PHPUnit_Framework_TestCase
{

    public function test_create_from_defaults(){
        $packages = Packages::createFromDefaults();
        $this->assertInstanceOf(Packages::class, $packages);
    }

    public function test_serialize_packages(){
        $packages = Packages::createFromDefaults();
        $json = json_encode($packages);
        $this->assertJson($json);
    }
}
