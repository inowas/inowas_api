<?php
/**
 * Created by PhpStorm.
 * User: Ralf
 * Date: 30.06.16
 * Time: 20:52
 */

namespace Tests\AppBundle\Model\GeoJson;


use AppBundle\Model\GeoJson\Polygon;

class PolygonTest extends \PHPUnit_Framework_TestCase
{

    public function testIstantiate(){
        $polygon = new Polygon();
        $polygon->setCoordinates(array(
            array(
                array(1, 2),
                array(2, 3),
                array(3, 4),
                array(4, 5),
                array(1, 2)
            )
        ));

        $this->assertInstanceOf('AppBundle\Model\GeoJson\Polygon', $polygon);
    }
}
