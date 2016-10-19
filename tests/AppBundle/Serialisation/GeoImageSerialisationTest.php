<?php

namespace Tests\AppBundle\Serialization;

use AppBundle\Entity\Raster;
use Inowas\PyprocessingBundle\Model\GeoImage\GeoImageProperties;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\RasterFactory;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class GeoTiffSerialisationTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var  \Inowas\PyprocessingBundle\Model\GeoImage\GeoImageProperties $geoTiffProperties */
    protected $geoTiffProperties;

    /** @var  Raster $raster */
    protected $raster;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();

        $this->raster = RasterFactory::create()
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
            ));

        $activeCells = array(
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
        );

        $this->geoTiffProperties = new GeoImageProperties($this->raster, $activeCells, 'cr_test', 3853, 'test');
    }

    public function testInterpolationSerialization()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('geoimage');
        $geoTiffPropertiesJSON = $this->serializer->serialize($this->geoTiffProperties, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$geoTiffPropertiesJSON);
        $geoTiffProperties = json_decode($geoTiffPropertiesJSON);

        $this->assertObjectHasAttribute('color_scheme', $geoTiffProperties);
        $this->assertEquals($geoTiffProperties->color_scheme, 'cr_test');
        $this->assertObjectHasAttribute('bounding_box', $geoTiffProperties);
        $this->assertObjectHasAttribute('x_min', $geoTiffProperties->bounding_box);
        $this->assertEquals($this->raster->getBoundingBox()->getXMin(), $geoTiffProperties->bounding_box->x_min);
        $this->assertObjectHasAttribute('x_max', $geoTiffProperties->bounding_box);
        $this->assertEquals($this->raster->getBoundingBox()->getXMax(), $geoTiffProperties->bounding_box->x_max);
        $this->assertObjectHasAttribute('y_min', $geoTiffProperties->bounding_box);
        $this->assertEquals($this->raster->getBoundingBox()->getYMin(), $geoTiffProperties->bounding_box->y_min);
        $this->assertObjectHasAttribute('y_max', $geoTiffProperties->bounding_box);
        $this->assertEquals($this->raster->getBoundingBox()->getYMax(), $geoTiffProperties->bounding_box->y_max);
        $this->assertObjectHasAttribute('no_data_val', $geoTiffProperties);
        $this->assertEquals($this->raster->getNoDataVal(), $geoTiffProperties->no_data_val);
        $this->assertObjectHasAttribute('target_projection', $geoTiffProperties);
        $this->assertEquals(3853, $geoTiffProperties->target_projection);
        $this->assertObjectHasAttribute('output_format', $geoTiffProperties);
        $this->assertEquals('test', $geoTiffProperties->output_format);
        $this->assertObjectHasAttribute('min', $geoTiffProperties);
        $this->assertEquals('10%', $geoTiffProperties->min);
        $this->assertObjectHasAttribute('max', $geoTiffProperties);
        $this->assertEquals('90%', $geoTiffProperties->max);
    }
}