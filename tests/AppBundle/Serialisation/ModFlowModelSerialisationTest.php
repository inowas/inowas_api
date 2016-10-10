<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\ActiveCells;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\UserFactory;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class ModflowModelSerialisationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var ModFlowModel $modflowModel */
    protected $modflowModel;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();

        $this->modflowModel = ModFlowModelFactory::create()
            ->setName('ModFlowModel')
            ->setOwner(UserFactory::create())
            ->setArea(AreaFactory::create())
            ->setActiveCells(ActiveCells::fromArray(
                array(
                    array(1,2,3),
                    array(1,2,3),
                    array(1,2,3)
                ))
            )
            ->setGridSize(new GridSize(4,5))
            ->setBoundingBox(new BoundingBox(5,6,7,9,4326))
        ;
    }

    public function testSoilModelListSerialisation()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelParameters');

        $serializedModel = $this->serializer->serialize($this->modflowModel, 'json', $serializationContext);
        $this->assertJson($serializedModel);
    }
}
