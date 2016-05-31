<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GaussianInterpolation;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\PointValue;
use AppBundle\Service\Interpolation;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class InterpolationSerialisationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var  Interpolation $interpolation */
    protected $interpolation;

    /** @var  BoundingBox $boundingBox */
    protected $boundingBox;

    /** @var  GridSize $gridSize */
    protected $gridSize;


    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();

        $this->gridSize = new GridSize(10,11);
        $this->boundingBox = new BoundingBox(0.1, 10.2, 0.4, 10.5);

        $this->interpolation = new GaussianInterpolation();
        $this->interpolation->setGridSize($this->gridSize);
        $this->interpolation->setBoundingBox($this->boundingBox);

        $this->interpolation->addPoint(new PointValue(1, 5, 3));
        $this->interpolation->addPoint(new PointValue(2, 8, 3));
        $this->interpolation->addPoint(new PointValue(7, 2, 3));
    }

    public function testInterpolation()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('interpolation');
        $interpolation = $this->serializer->serialize($this->interpolation, 'json', $serializationContext);

        $this->assertStringStartsWith('{',$interpolation);
        $interpolation = json_decode($interpolation);
        $this->assertObjectHasAttribute('type', $interpolation);
        $this->assertEquals($interpolation->type, Interpolation::TYPE_GAUSSIAN);
        $this->assertObjectHasAttribute('bounding_box', $interpolation);
        $this->assertObjectHasAttribute('x_min', $interpolation->bounding_box);
        $this->assertEquals($this->boundingBox->getXMin(), $interpolation->bounding_box->x_min);
        $this->assertObjectHasAttribute('x_max', $interpolation->bounding_box);
        $this->assertEquals($this->boundingBox->getXMax(), $interpolation->bounding_box->x_max);
        $this->assertObjectHasAttribute('y_min', $interpolation->bounding_box);
        $this->assertEquals($this->boundingBox->getYMin(), $interpolation->bounding_box->y_min);
        $this->assertObjectHasAttribute('y_max', $interpolation->bounding_box);
        $this->assertEquals($this->boundingBox->getYMax(), $interpolation->bounding_box->y_max);
        $this->assertObjectHasAttribute('grid_size', $interpolation);
        $this->assertObjectHasAttribute('n_x', $interpolation->grid_size);
        $this->assertEquals($this->gridSize->getNX(), $interpolation->grid_size->n_x);
        $this->assertObjectHasAttribute('n_y', $interpolation->grid_size);
        $this->assertEquals($this->gridSize->getNY(), $interpolation->grid_size->n_y);
        $this->assertObjectHasAttribute('point_values', $interpolation);
        $this->assertCount(3, $interpolation->point_values);
        $this->assertObjectHasAttribute('x', $interpolation->point_values[0]);
        $this->assertObjectHasAttribute('y', $interpolation->point_values[0]);
        $this->assertObjectHasAttribute('value', $interpolation->point_values[0]);
    }
}
