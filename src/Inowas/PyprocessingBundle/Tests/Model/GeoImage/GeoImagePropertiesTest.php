<?php

namespace Inowas\PyprocessingBundle\Tests\Model\GeoImage;

use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\RasterFactory;
use Inowas\PyprocessingBundle\Model\GeoImage\GeoImageProperties;

class GeoImagePropertiesTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate(){

        $geoImageProperties = new GeoImageProperties(
            RasterFactory::create()
                ->setBoundingBox(new BoundingBox(0.1, 10.2, 0.4, 10.5))
                ->setGridSize(new GridSize(10,11))
                ->setData(array(
                    array(1,2,3,4,5,6,7,8,9,10),
                    array(1,2,3,4,5,6,7,8,9,10),
                    array(1,2,3,4,5,6,7,8,9,10),
                    array(1,2,3,4,5,6,7,8,9,10),
                    array(1,2,3,4,5,6,7,8,9,10),
                    array(1,2,3,4,5,6,7,8,9,10),
                    array(1,2,3,4,5,6,7,8,9,10),
                    array(1,2,3,4,5,6,7,8,9,10),
                    array(1,2,3,4,5,6,7,8,9,10),
                    array(1,2,3,4,5,6,7,8,9,10),
                    array(1,2,3,4,5,6,7,8,9,10),
                )),
            array(
                array(1,1,1,1,1,1,1,1,1,1),
                array(1,1,1,1,1,1,1,1,1,1),
                array(1,1,1,1,1,1,1,1,1,1),
                array(1,1,1,1,1,1,1,1,1,1),
                array(1,1,1,1,1,1,1,1,1,1),
                array(1,1,1,1,1,1,1,1,1,1),
                array(1,1,1,1,1,1,1,1,1,1),
                array(1,1,1,1,1,1,1,1,1,1),
                array(1,1,1,1,1,1,1,1,1,1),
                array(1,1,1,1,1,1,1,1,1,1),
                array(1,1,1,1,1,1,1,1,1,1)
            ),
            'cr_test',
            3854,
            'test');

        $this->assertInstanceOf(GeoImageProperties::class, $geoImageProperties);

        $expected = '{"bounding_box":{"x_min":0.1,"x_max":10.2,"y_min":0.4,"y_max":10.5,"srid":0},"data":[[1,2,3,4,5,6,7,8,9,10],[1,2,3,4,5,6,7,8,9,10],[1,2,3,4,5,6,7,8,9,10],[1,2,3,4,5,6,7,8,9,10],[1,2,3,4,5,6,7,8,9,10],[1,2,3,4,5,6,7,8,9,10],[1,2,3,4,5,6,7,8,9,10],[1,2,3,4,5,6,7,8,9,10],[1,2,3,4,5,6,7,8,9,10],[1,2,3,4,5,6,7,8,9,10],[1,2,3,4,5,6,7,8,9,10]],"no_data_val":-9999,"color_scheme":"cr_test","target_projection":3854,"output_format":"test","min":"10%","max":"90%"}';
        $this->assertEquals(json_decode($expected), json_decode(json_encode($geoImageProperties)));
    }
}
