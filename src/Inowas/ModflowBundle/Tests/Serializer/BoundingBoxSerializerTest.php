<?php

namespace Inowas\ModflowBundle\Tests\Serializer;

use Inowas\ModflowBundle\Model\BoundingBox;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BoundingBoxSerializerTest extends KernelTestCase {

    /** @var  BoundingBox */
    protected $boundingBox;

    /** @var  Serializer */
    protected $serializer;

    public function setUp()
    {
        self::bootKernel();
        $this->boundingBox = new BoundingBox(1, 2, 3, 4, 4326);
        $this->serializer = static::$kernel->getContainer()
            ->get('jms_serializer')
        ;
    }

    public function testSerialize()
    {
        $json = $this->serializer
            ->serialize(
                $this->boundingBox,
                'json',
                SerializationContext::create()
                    ->setGroups(array('details'))
            );

        $this->assertJson($json);
        $response = json_decode($json);
        $this->assertObjectHasAttribute('x_min', $response);
        $this->assertEquals($this->boundingBox->getXMin(), $response->x_min);
        $this->assertObjectHasAttribute('x_max', $response);
        $this->assertEquals($this->boundingBox->getXMax(), $response->x_max);
        $this->assertObjectHasAttribute('y_min', $response);
        $this->assertEquals($this->boundingBox->getYMin(), $response->y_min);
        $this->assertObjectHasAttribute('y_max', $response);
        $this->assertEquals($this->boundingBox->getYMax(), $response->y_max);
        $this->assertObjectHasAttribute('srid', $response);
        $this->assertEquals($this->boundingBox->getSrid(), $response->srid);
    }

    public function tearDown()
    {
        unset($this->boundingBox);
        unset($this->serializer);
    }

}