<?php

namespace Inowas\ModflowBundle\Tests\Serializer;

use Inowas\ModflowBundle\Model\ActiveCells;
use Inowas\ModflowBundle\Model\Area;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ModelObjectSerializerTest extends KernelTestCase {

    /** @var  Area */
    protected $area;

    /** @var  Serializer */
    protected $serializer;

    public function setUp()
    {
        self::bootKernel();
        $this->area = new Area();
        $this->area->setName('MyArea');
        $this->area->setActiveCells(ActiveCells::fromArray(array(
            [1,2,3,4,5],
            [1,2,3,4,5],
            [1,2,3,4,5],
            [1,2,3,4,5]
        )));
        $this->area->setDateCreated(new \DateTime('2015-01-01'));
        $this->area->setDateModified(new \DateTime('2015-01-02'));

        $this->serializer = static::$kernel->getContainer()
            ->get('jms_serializer')
        ;
    }

    public function testSerialize(){
        $json = $this->serializer
            ->serialize($this->area, 'json',
                SerializationContext::create()
                    ->setGroups(array('details'))
            );

        $this->assertJson($json);
        $response = json_decode($json);

        $this->assertObjectHasAttribute('id', $response);
        $this->assertEquals($this->area->getId()->toString(), $response->id);
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals($this->area->getName(), $response->name);
        $this->assertObjectHasAttribute('active_cells', $response);
        $this->assertObjectHasAttribute('date_created', $response);
        $this->assertEquals($this->area->getDateCreated(), new \DateTime($response->date_created));
        $this->assertObjectHasAttribute('date_modified', $response);
        $this->assertEquals($this->area->getDateModified(), new \DateTime($response->date_modified));
    }

    public function tearDown()
    {
        unset($this->area);
        unset($this->serializer);
    }

}