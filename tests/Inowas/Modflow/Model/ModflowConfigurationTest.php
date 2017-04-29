<?php

namespace Tests\Inowas\Modflow\Model\Packages;

use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\PackageName;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\ModflowConfiguration;

class ModflowConfigurationTest extends \PHPUnit_Framework_TestCase
{

    public function test_create_from_defaults(){
        $packages = ModflowConfiguration::createFromDefaultsWithId(ModflowId::generate());
        $this->assertInstanceOf(ModflowConfiguration::class, $packages);
    }

    public function test_serialize_packages(){
        $packages = ModflowConfiguration::createFromDefaultsWithId(ModflowId::generate());
        $json = json_encode($packages);
        $this->assertJson($json);
    }

    public function test_create_from_array(){
        $packages = ModflowConfiguration::createFromDefaultsWithId(ModflowId::generate());
        $json = json_encode($packages);
        $this->assertJson($json);

        $obj = \json_decode($json);
        $obj->author = "Ralf Junghanns";
        $json = \json_encode($obj);

        $packages = ModflowConfiguration::fromJson($json);
        $this->assertInstanceOf(ModflowConfiguration::class, $packages);
        $this->assertEquals( "Ralf Junghanns", $packages->author());
    }

    public function test_update_default_time_unit(){
        $packages = ModflowConfiguration::createFromDefaultsWithId(ModflowId::generate());
        $json = json_encode($packages);
        $this->assertJson($json);
        $obj = \json_decode($json);
        $this->assertEquals(4, $obj->data->dis->itmuni);
    }

    public function test_update_time_unit(){
        $packages = ModflowConfiguration::createFromDefaultsWithId(ModflowId::generate());
        $packages->updateTimeUnit(TimeUnit::fromInt(TimeUnit::SECONDS));
        $json = json_encode($packages);
        $this->assertJson($json);
        $obj = \json_decode($json);
        $this->assertEquals(1, $obj->data->dis->itmuni);
    }

    public function test_update_time_unit_with_update_param_function()
    {
        $packages = ModflowConfiguration::createFromDefaultsWithId(ModflowId::generate());
        $packages->updatePackageParameter('dis', 'TimeUnit', TimeUnit::fromInt(TimeUnit::MINUTES));
        $json = json_encode($packages);
        $this->assertJson($json);
        $obj = \json_decode($json);
        $this->assertEquals(2, $obj->data->dis->itmuni);
    }

    public function test_gridsize_has_same_size_as_ibound(): void
    {
        $gridsize = GridSize::fromXY(40,50);
        $boundingBox = BoundingBox::fromEPSG4326Coordinates(10,20,30,40,100,200);
        $packages = ModflowConfiguration::createFromDefaultsWithId(ModflowId::generate());
        $packages->updateGridParameters($gridsize, $boundingBox);

    }

    public function test_change_flow_package(): void
    {
        $configuration = ModflowConfiguration::createFromDefaultsWithId(ModflowId::generate());
        $configuration->changeFlowPackage(PackageName::fromString('upw'));
        json_encode($configuration);
    }
}
