<?php

namespace Tests\Inowas\Modflow\Model\Packages;

use Inowas\Common\Modflow\TimeUnit;
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

    public function test_update_default_time_unit(){
        $packages = Packages::createFromDefaults();
        $json = json_encode($packages);
        $this->assertJson($json);
        $obj = \json_decode($json);
        $this->assertEquals(4, $obj->data->dis->itmuni);
    }

    public function test_update_time_unit(){
        $packages = Packages::createFromDefaults();
        $packages->updateTimeUnit(TimeUnit::fromInt(TimeUnit::SECONDS));
        $json = json_encode($packages);
        $this->assertJson($json);
        $obj = \json_decode($json);
        $this->assertEquals(1, $obj->data->dis->itmuni);
    }

    public function test_update_time_unit_with_update_param_function(){
        $packages = Packages::createFromDefaults();
        $packages->updatePackageParameter('dis', 'TimeUnit', TimeUnit::fromInt(TimeUnit::MINUTES));
        $json = json_encode($packages);
        $this->assertJson($json);
        $obj = \json_decode($json);
        $this->assertEquals(2, $obj->data->dis->itmuni);
    }
}
