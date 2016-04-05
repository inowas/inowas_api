<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Boundary;
use AppBundle\Model\BoundaryFactory;
use AppBundle\Model\ObservationPointFactory;
use AppBundle\Model\UserFactory;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class BoundarySerialisationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var Boundary $boundary */
    protected $boundary;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();

        $this->boundary = BoundaryFactory::create()
            ->setId(12)
            ->setName('BoundaryName')
            ->setPublic(true)
            ->setOwner(
                UserFactory::createTestUser('BoundaryTestUser')
                ->setId(11)
            )
            ->setGeometry(new LineString(
                array(
                    new Point(11777056.49104572273790836, 2403440.17028302047401667),
                    new Point(11777973.9436037577688694, 2403506.49811625294387341),
                    new Point(11780228.12698311358690262, 2402856.2682070448063314),
                    new Point(11781703.59880801662802696, 2401713.22520185634493828)
                ), 5432))
            ->addObservationPoint(ObservationPointFactory::create()
                ->setId(21)
                ->setName('ObservationPointName')
            )
        ;
    }

    public function testBoundaryDetails()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');
        $boundary = $this->serializer->serialize($this->boundary, 'json', $serializationContext);

        $this->assertStringStartsWith('{', $boundary);
        $boundary = json_decode($boundary);
        $this->assertEquals($boundary->type, 'boundary');
        $this->assertEquals($boundary->owner->id, $this->boundary->getOwner()->getId());
        $this->assertEquals((array) $boundary->geometry[0], (array)$this->boundary->serializeDeserializeGeometry()[0]);
        $this->assertCount(1, $this->boundary->getObservationPoints());
        $this->assertEquals($boundary->observation_points[0]->id, $this->boundary->getObservationPoints()->first()->getId());
        $this->assertEquals($boundary->observation_points[0]->name, $this->boundary->getObservationPoints()->first()->getName());
    }
}
