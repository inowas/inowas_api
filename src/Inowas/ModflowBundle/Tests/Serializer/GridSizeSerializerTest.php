<?php

namespace Inowas\ModflowBundle\Tests\Serializer;

use Inowas\ModflowBundle\Model\BoundaryFactory;
use Inowas\ModflowBundle\Model\BoundingBox;
use Inowas\ModflowBundle\Model\GridSize;
use Inowas\ModflowBundle\Service\ModflowToolManager;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GridSizeSerializerTest extends KernelTestCase {

    /** @var  GridSize */
    protected $gridSize;

    /** @var  Serializer */
    protected $serializer;

    public function setUp()
    {
        self::bootKernel();
        $this->gridSize = new GridSize(50, 60);
        $this->serializer = static::$kernel->getContainer()
            ->get('jms_serializer')
        ;
    }

    public function testSerialize(){
        $json = $this->serializer
            ->serialize(
                $this->gridSize,
                'json',
                SerializationContext::create()
                    ->setGroups(array('details'))
            );

        $this->assertJson($json);
        $response = json_decode($json);
        $this->assertObjectHasAttribute('n_x', $response);
        $this->assertEquals($this->gridSize->getNX(), $response->n_x);
        $this->assertObjectHasAttribute('n_y', $response);
        $this->assertEquals($this->gridSize->getNY(), $response->n_y);
    }

    public function tearDown()
    {
        unset($this->gridSize);
        unset($this->serializer);
    }

}