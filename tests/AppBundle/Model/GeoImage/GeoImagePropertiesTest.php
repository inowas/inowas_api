<?php

namespace Tests\AppBundle\Model\GeoImage;


use AppBundle\Model\GeoImage\GeoImageProperties;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\RasterFactory;

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

        $this->assertInstanceOf('AppBundle\Model\GeoImage\GeoImageProperties', $geoImageProperties);
    }
}
