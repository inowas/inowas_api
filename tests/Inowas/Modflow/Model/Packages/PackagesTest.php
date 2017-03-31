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

    public function test_create_from_array(){
        $packages = Packages::createFromDefaults();
        $json = json_encode($packages);
        $this->assertJson($json);

        $obj = \json_decode($json);
        $obj->author = "Ralf Junghanns";
        $json = \json_encode($obj);

        $packages = Packages::fromJson($json);
        $this->assertInstanceOf(Packages::class, $packages);
        $this->assertEquals( "Ralf Junghanns", $packages->author());
    }
}
