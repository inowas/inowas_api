<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Area;
use AppBundle\Entity\AreaType;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\AreaTypeFactory;
use AppBundle\Entity\User;
use AppBundle\Model\UserFactory;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class AreaSerialisationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var User $owner */
    protected $owner;

    /** @var AreaType $areaType*/
    protected $areaType;

    /** @var  Area $area */
    protected $area;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();
        $this->owner = UserFactory::createTestUser("ModelTest_Owner");
        $this->areaType = AreaTypeFactory::setName('ModelAreaType');
        $this->area = AreaFactory::create()
            ->setAreaType(AreaTypeFactory::setName("AreaType"))
        ;

        $rings = array(
            new LineString(
                array(
                    new Point(11777056.49104572273790836, 2403440.17028302047401667),
                    new Point(11777973.9436037577688694, 2403506.49811625294387341),
                    new Point(11780228.12698311358690262, 2402856.2682070448063314),
                    new Point(11781703.59880801662802696, 2401713.22520185634493828),
                    new Point(11782192.89715446159243584, 2400859.20254275016486645),
                    new Point(11782678.03379831649363041, 2399224.82580633740872145),
                    new Point(11782955.64566324092447758, 2398372.03099954081699252),
                    new Point(11783586.59488865174353123, 2397659.24991086078807712),
                    new Point(11784427.14815393835306168, 2396590.66674219723790884),
                    new Point(11784914.27011025696992874, 2395382.18267500726506114),
                    new Point(11785330.82068796083331108, 2394174.15454542031511664),
                    new Point(11785536.96124399080872536, 2393180.11378513323143125),
                    new Point(11786097.1273522675037384, 2392467.84464810928329825),
                    new Point(11787011.69080197438597679, 2392108.19440084183588624),
                    new Point(11787715.90038010291755199, 2391962.42985267844051123),
                    new Point(11788487.82464707084000111, 2391319.86146369902417064),
                    new Point(11789680.65233467146754265, 2390320.33801258727908134),
                    new Point(11789747.53923093341290951, 2389681.79035578016191721),
                    new Point(11789176.05731181986629963, 2388337.88133400911465287),
                    new Point(11788252.26803966984152794, 2386996.03587882174178958),
                    new Point(11787540.82363948784768581, 2385794.83458124194294214),
                    new Point(11783036.01740818470716476, 2386882.81766726961359382),
                    new Point(11777486.37431096099317074, 2390598.53498441586270928),
                    new Point(11775189.21765423379838467, 2396638.4036272126249969),
                    new Point(11777056.49104572273790836, 2403440.17028302047401667)
                )
            )
        );

        /** @var Polygon $polygon */
        $polygon = new Polygon($rings, 3857);
        $this->area->setGeometry($polygon);
    }

    public function testAreaDetails()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');
        $area = $this->serializer->serialize($this->area, 'json', $serializationContext);

        $this->assertStringStartsWith('{',$area);
        $area = json_decode($area);
        $this->assertEquals($area->type, 'area');
        $this->assertEquals($area->area_type->name, $this->area->getAreaType()->getName());
        $this->assertEquals((array) $area->geometry[0], (array)$this->area->serializeDeserializeGeometry()[0]);
    }
}
