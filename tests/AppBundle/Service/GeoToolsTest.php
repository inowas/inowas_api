<?php

namespace AppBundle\Tests\Service;

use AppBundle\Entity\Area;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\ConstantHeadBoundaryFactory;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Point;
use AppBundle\Service\GeoTools;
use CrEOF\Spatial\DBAL\Platform\PostgreSql;
use CrEOF\Spatial\DBAL\Types\AbstractSpatialType;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GeoToolsTest extends WebTestCase
{
    /** @var  GeoTools */
    protected $geoTools;

    /** @var  Area */
    protected $area;

    /** @var  BoundingBox */
    protected $boundingBox;

    /** @var  GridSize $gridSize */
    protected $gridSize;

    /** @var  EntityManager */
    protected $entityManager;

    public function setUp()
    {
        self::bootKernel();

        $this->geoTools = static::$kernel->getContainer()
            ->get('inowas.geotools');

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.entity_manager');

        $this->area = AreaFactory::create();
        $converter = new PostgreSql();
        $geometryText = "Polygon ((11777056.49104572273790836 2403440.17028302047401667, 11777973.9436037577688694 2403506.49811625294387341, 11780228.12698311358690262 2402856.2682070448063314, 11781703.59880801662802696 2401713.22520185634493828, 11782192.89715446159243584 2400859.20254275016486645, 11782678.03379831649363041 2399224.82580633740872145, 11782955.64566324092447758 2398372.03099954081699252, 11783586.59488865174353123 2397659.24991086078807712, 11784427.14815393835306168 2396590.66674219723790884, 11784914.27011025696992874 2395382.18267500726506114, 11785330.82068796083331108 2394174.15454542031511664, 11785536.96124399080872536 2393180.11378513323143125, 11786097.1273522675037384 2392467.84464810928329825, 11787011.69080197438597679 2392108.19440084183588624, 11787715.90038010291755199 2391962.42985267844051123, 11788487.82464707084000111 2391319.86146369902417064, 11789680.65233467146754265 2390320.33801258727908134, 11789747.53923093341290951 2389681.79035578016191721, 11789176.05731181986629963 2388337.88133400911465287, 11788252.26803966984152794 2386996.03587882174178958, 11787540.82363948784768581 2385794.83458124194294214, 11783036.01740818470716476 2386882.81766726961359382, 11777486.37431096099317074 2390598.53498441586270928, 11775189.21765423379838467 2396638.4036272126249969, 11777056.49104572273790836 2403440.17028302047401667))";

        /** @var AbstractSpatialType $polygonType */
        $polygonType = Type::getType('polygon');

        /** @var Polygon $polygon */
        $polygon = $converter->convertStringToPHPValue($polygonType, $geometryText);
        $polygon->setSrid(3857);
        $this->area->setGeometry($polygon);

        $this->entityManager->persist($this->area);
        $this->entityManager->flush();

        $this->boundingBox = new BoundingBox(
            11775189.21765423379838467,
            11789747.53923093341290951,
            2385794.83458124194294214,
            2403506.49811625294387341,
            3857);

        $this->gridSize = new GridSize(5,5);
    }

    public function testReturnsPropertyTypeByAbbreviationIfExists(){
        $result = $this->geoTools->calculateActiveCells($this->area, $this->boundingBox, $this->gridSize);
        $this->assertCount(5, $result);
        $this->assertCount(5, $result[0]);
    }

    public function testGetGeometrySRID3857FromConstantHeadBoundaryAsGeoJSON(){
        $chb = ConstantHeadBoundaryFactory::create()
            ->setName('CHB1')
            ->setGeometry(new LineString(array(
                array(11787540.82363948784768581, 2385794.83458124194294214),
                array(11783036.01740818470716476, 2386882.81766726961359382),
                array(11777486.37431096099317074, 2390598.53498441586270928),
                array(11775189.21765423379838467, 2396638.40362721262499690),
                array(11777056.49104572273790836, 2403440.17028302047401667),
            ), 3857));

        $this->entityManager->persist($chb);
        $this->entityManager->flush();
        $result = json_decode($this->geoTools->getGeometryFromModelObjectAsGeoJSON($chb, 3857));
        $this->assertObjectHasAttribute('type', $result);
        $this->assertEquals('LineString', $result->type);
        $this->assertObjectHasAttribute('coordinates', $result);
        $this->assertCount(5, $result->coordinates);
        $this->assertEquals(11787540.823639, $result->coordinates[0][0]);
        $this->assertEquals(2385794.8345812, $result->coordinates[0][1]);
    }

    public function testGetGeometrySRID4326FromConstantHeadBoundaryAsGeoJSON(){
        $chb = ConstantHeadBoundaryFactory::create()
            ->setName('CHB1')
            ->setGeometry(new LineString(array(
                array(11787540.82363948784768581, 2385794.83458124194294214),
                array(11783036.01740818470716476, 2386882.81766726961359382),
                array(11777486.37431096099317074, 2390598.53498441586270928),
                array(11775189.21765423379838467, 2396638.40362721262499690),
                array(11777056.49104572273790836, 2403440.17028302047401667),
            ), 3857));

        $this->entityManager->persist($chb);
        $this->entityManager->flush();
        $result = json_decode($this->geoTools->getGeometryFromModelObjectAsGeoJSON($chb, 4326));
        $this->assertObjectHasAttribute('type', $result);
        $this->assertEquals('LineString', $result->type);
        $this->assertObjectHasAttribute('coordinates', $result);
        $this->assertCount(5, $result->coordinates);
        $this->assertEquals(105.88928084058, $result->coordinates[0][0]);
        $this->assertEquals(20.948969914319, $result->coordinates[0][1]);
    }

    public function testTransformPoint()
    {
        $point = new Point(11777056.49104572273790836, 2403440.17028302047401667, 3857);
        $targetSrid = 4326;
        $transformedPoint = $this->geoTools->transformPoint($point, $targetSrid);
        $this->assertTrue($transformedPoint instanceof Point);
        $this->assertEquals(105.79509847846, $transformedPoint->getX());
        $this->assertEquals(21.096929627229, $transformedPoint->getY());
        $this->assertEquals($targetSrid, $transformedPoint->getSrid());
    }

    public function testTransformBoundingBox()
    {
        $boundingBox = new BoundingBox(578205, 594692, 2316000, 2333500, 32648);
        $targetSrid = 4326;
        $transformedBoundingBox = $this->geoTools->transformBoundingBox($boundingBox, $targetSrid);

        $this->assertTrue($transformedBoundingBox instanceof BoundingBox);
        $this->assertEquals(105.75218379342, $transformedBoundingBox->getXMin());
        $this->assertEquals(105.91170436595, $transformedBoundingBox->getXMax());
        $this->assertEquals(20.942793923555, $transformedBoundingBox->getYMin());
        $this->assertEquals(21.100124603334, $transformedBoundingBox->getYMax());
        $this->assertEquals($targetSrid, $transformedBoundingBox->getSrid());
    }

    public function tearDown()
    {
        $area = $this->entityManager->getRepository('AppBundle:Area')
            ->findOneBy(array(
                'id' => $this->area->getId()->toString()
            ));

        $this->entityManager->remove($area);
        $this->entityManager->flush();
    }
}