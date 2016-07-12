<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GaussianInterpolation;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\PointValue;
use AppBundle\Model\Point;
use AppBundle\Service\Interpolation;
use AppBundle\Process\Interpolation\InterpolationParameter;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class InterpolationSerialisationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var  Interpolation $interpolation */
    protected $interpolation;

    /** @var  InterpolationParameter $interpolationParameter */
    protected $interpolationParameter;

    /** @var  BoundingBox $boundingBox */
    protected $boundingBox;

    /** @var  GridSize $gridSize */
    protected $gridSize;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();
        $this->gridSize = new GridSize(10,11);
        $this->boundingBox = new BoundingBox(0.1, 10.2, 0.4, 10.5);

        $this->interpolationParameter = new InterpolationParameter(
            $this->gridSize,
            $this->boundingBox,
            array(
                new PointValue(new Point(1, 5, 4326), 3),
                new PointValue(new Point(2, 8, 4326), 3),
                new PointValue(new Point(7, 2, 4326), 3)
            ),
            array(Interpolation::TYPE_GAUSSIAN)
        );

        $this->interpolation = new GaussianInterpolation($this->interpolationParameter);
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
