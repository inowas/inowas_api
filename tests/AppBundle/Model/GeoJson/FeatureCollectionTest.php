<?php

namespace Tests\AppBundle\Model\GeoJson;


use AppBundle\Model\GeoJson\Feature;
use AppBundle\Model\GeoJson\FeatureCollection;

class FeatureCollectionTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate()
    {
        $featureCollection = new FeatureCollection();
        $featureCollection->addFeature(new Feature(1));
        $this->assertInstanceOf('AppBundle\Model\GeoJson\FeatureCollection', $featureCollection);
    }

}

