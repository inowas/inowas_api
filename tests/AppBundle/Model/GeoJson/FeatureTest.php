<?php

namespace Tests\AppBundle\Model\GeoJson;

use AppBundle\Model\GeoJson\Feature;
use AppBundle\Model\GeoJson\Polygon;
use AppBundle\Model\GeoJson\Properties;

class FeatureTest extends \PHPUnit_Framework_TestCase
{

    public function testFeatureInstantiation()
    {
        $feature = new Feature(1);
        $feature->setProperties(new Properties());
        $feature->setGeometry(new Polygon());
        $this->assertInstanceOf('AppBundle\Model\GeoJson\Feature', $feature);
    }

}
