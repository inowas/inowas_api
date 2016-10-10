<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\StreamBoundary;
use AppBundle\Model\Point;
use AppBundle\Model\StreamBoundaryFactory;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class StreamSerialisationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var StreamBoundary $stream */
    protected $stream;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();
        
        $this->stream = StreamBoundaryFactory::create()
            ->setName('StreamName')
            ->setStartingPoint(new Point(11777056.49104572273790836, 2403440.17028302047401667, 3452))
            ->setGeometry(new LineString(
                array(
                    new Point(11777056.49104572273790836, 2403440.17028302047401667),
                    new Point(11777973.9436037577688694, 2403506.49811625294387341),
                    new Point(11780228.12698311358690262, 2402856.2682070448063314)
                ), 3452)
            );
    }

    public function testAreaDetails()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');
        $stream = $this->serializer->serialize($this->stream, 'json', $serializationContext);

        $this->assertStringStartsWith('{',$stream);
        $stream = json_decode($stream);
        $this->assertEquals('RIV', $stream->type);
        $this->assertEquals((array) $stream->line, $this->stream->serializeDeserializeLine());
        $this->assertEquals((array) $stream->starting_point, $this->stream->serializeDeserializeStartingPoint());
    }
}
