<?php

namespace Inowas\PyprocessingBundle\Tests\Service;

use AppBundle\Entity\Area;
use AppBundle\Entity\StreamBoundary;
use AppBundle\Model\ActiveCells;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\ConstantHeadBoundaryFactory;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\Point;
use AppBundle\Model\StreamBoundaryFactory;
use AppBundle\Service\GeoTools;
use CrEOF\Spatial\DBAL\Platform\PostgreSql;
use CrEOF\Spatial\DBAL\Types\AbstractSpatialType;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GeoToolsTest extends WebTestCase
{
    /** @var  GeoTools */
    protected $geoTools;

    /** @var  Area */
    protected $area;

    /** @var  StreamBoundary */
    protected $river;

    /** @var  BoundingBox */
    protected $boundingBox;

    /** @var  GridSize $gridSize */
    protected $gridSize;

    /** @var  EntityManager */
    protected $entityManager;

    /** @var  Serializer */
    protected $serializer;

    public function setUp()
    {
        self::bootKernel();

        $this->geoTools = static::$kernel->getContainer()
            ->get('inowas.geotools');

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.entity_manager');

        $this->serializer = static::$kernel->getContainer()
            ->get('jms_serializer');

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

        $this->river = StreamBoundaryFactory::create()
            ->setGeometry(new LineString(array(
                    array(11775189.21765423379838467, 2385794.83458124194294214),
                    array(11789747.53923093341290951, 2403506.49811625294387341)
                ),3857
            ));

        $this->entityManager->persist($this->river);
        $this->entityManager->flush();

        $this->boundingBox = new BoundingBox(
            11775189.21765423379838467,
            11789747.53923093341290951,
            2385794.83458124194294214,
            2403506.49811625294387341,
            3857);

        $this->gridSize = new GridSize(5,5);
    }

    public function testCalculateActiveCellsWithArea(){
        $result = $this->geoTools->getActiveCells($this->area, $this->boundingBox, $this->gridSize);
        $expected = array(
            array(1,1,1),
            array(1,1,1,1),
            array(1,1,1,1),
            array(1,1,1,1,1),
            array(1=>1, 2=>1, 3=>1, 4=>1)
        );

        $this->assertTrue($result instanceof ActiveCells);
        $this->assertEquals($expected, $result->toArray());
        $this->assertTrue($this->geoTools->pointIntersectsWithArea($this->area, 11780000, 2390000, 3857));
        $this->assertFalse($this->geoTools->pointIntersectsWithArea($this->area, 11770000, 2380000, 3857));
    }

    public function testCalculateActiveCellsWithRiver(){
        $result = $this->geoTools->getActiveCells($this->river, $this->boundingBox, $this->gridSize);
        $expected = array(
            array(3=>1, 4=>1),
            array(3=>1),
            array(1=>1, 2=>1, 3=>1),
            array(1,1),
            array(1)
        );

        $this->assertTrue($result instanceof ActiveCells);
        $this->assertEquals($expected, $result->toArray());
    }

    public function testGetGeoJsonGrid(){
        $activeCells = $this->geoTools->getActiveCells($this->area, $this->boundingBox, $this->gridSize);
        $featureCollection = $this->geoTools->getGeoJsonGrid($this->boundingBox, $this->gridSize, $activeCells);
        $json = $this->serializer->serialize($featureCollection, 'json');
        $this->assertEquals($json, '{"type":"FeatureCollection","features":[{"type":"Feature","id":0,"properties":{"row":0,"col":0},"geometry":{"type":"Polygon","coordinates":[[[11775189.217654,2399964.1654093],[11775189.217654,2403506.4981163],[11778100.88197,2403506.4981163],[11778100.88197,2399964.1654093],[11775189.217654,2399964.1654093]]]}},{"type":"Feature","id":1,"properties":{"row":0,"col":1},"geometry":{"type":"Polygon","coordinates":[[[11778100.88197,2399964.1654093],[11778100.88197,2403506.4981163],[11781012.546285,2403506.4981163],[11781012.546285,2399964.1654093],[11778100.88197,2399964.1654093]]]}},{"type":"Feature","id":2,"properties":{"row":0,"col":2},"geometry":{"type":"Polygon","coordinates":[[[11781012.546285,2399964.1654093],[11781012.546285,2403506.4981163],[11783924.2106,2403506.4981163],[11783924.2106,2399964.1654093],[11781012.546285,2399964.1654093]]]}},{"type":"Feature","id":3,"properties":{"row":1,"col":0},"geometry":{"type":"Polygon","coordinates":[[[11775189.217654,2396421.8327022],[11775189.217654,2399964.1654093],[11778100.88197,2399964.1654093],[11778100.88197,2396421.8327022],[11775189.217654,2396421.8327022]]]}},{"type":"Feature","id":4,"properties":{"row":1,"col":1},"geometry":{"type":"Polygon","coordinates":[[[11778100.88197,2396421.8327022],[11778100.88197,2399964.1654093],[11781012.546285,2399964.1654093],[11781012.546285,2396421.8327022],[11778100.88197,2396421.8327022]]]}},{"type":"Feature","id":5,"properties":{"row":1,"col":2},"geometry":{"type":"Polygon","coordinates":[[[11781012.546285,2396421.8327022],[11781012.546285,2399964.1654093],[11783924.2106,2399964.1654093],[11783924.2106,2396421.8327022],[11781012.546285,2396421.8327022]]]}},{"type":"Feature","id":6,"properties":{"row":1,"col":3},"geometry":{"type":"Polygon","coordinates":[[[11783924.2106,2396421.8327022],[11783924.2106,2399964.1654093],[11786835.874916,2399964.1654093],[11786835.874916,2396421.8327022],[11783924.2106,2396421.8327022]]]}},{"type":"Feature","id":7,"properties":{"row":2,"col":0},"geometry":{"type":"Polygon","coordinates":[[[11775189.217654,2392879.4999952],[11775189.217654,2396421.8327022],[11778100.88197,2396421.8327022],[11778100.88197,2392879.4999952],[11775189.217654,2392879.4999952]]]}},{"type":"Feature","id":8,"properties":{"row":2,"col":1},"geometry":{"type":"Polygon","coordinates":[[[11778100.88197,2392879.4999952],[11778100.88197,2396421.8327022],[11781012.546285,2396421.8327022],[11781012.546285,2392879.4999952],[11778100.88197,2392879.4999952]]]}},{"type":"Feature","id":9,"properties":{"row":2,"col":2},"geometry":{"type":"Polygon","coordinates":[[[11781012.546285,2392879.4999952],[11781012.546285,2396421.8327022],[11783924.2106,2396421.8327022],[11783924.2106,2392879.4999952],[11781012.546285,2392879.4999952]]]}},{"type":"Feature","id":10,"properties":{"row":2,"col":3},"geometry":{"type":"Polygon","coordinates":[[[11783924.2106,2392879.4999952],[11783924.2106,2396421.8327022],[11786835.874916,2396421.8327022],[11786835.874916,2392879.4999952],[11783924.2106,2392879.4999952]]]}},{"type":"Feature","id":11,"properties":{"row":3,"col":0},"geometry":{"type":"Polygon","coordinates":[[[11775189.217654,2389337.1672882],[11775189.217654,2392879.4999952],[11778100.88197,2392879.4999952],[11778100.88197,2389337.1672882],[11775189.217654,2389337.1672882]]]}},{"type":"Feature","id":12,"properties":{"row":3,"col":1},"geometry":{"type":"Polygon","coordinates":[[[11778100.88197,2389337.1672882],[11778100.88197,2392879.4999952],[11781012.546285,2392879.4999952],[11781012.546285,2389337.1672882],[11778100.88197,2389337.1672882]]]}},{"type":"Feature","id":13,"properties":{"row":3,"col":2},"geometry":{"type":"Polygon","coordinates":[[[11781012.546285,2389337.1672882],[11781012.546285,2392879.4999952],[11783924.2106,2392879.4999952],[11783924.2106,2389337.1672882],[11781012.546285,2389337.1672882]]]}},{"type":"Feature","id":14,"properties":{"row":3,"col":3},"geometry":{"type":"Polygon","coordinates":[[[11783924.2106,2389337.1672882],[11783924.2106,2392879.4999952],[11786835.874916,2392879.4999952],[11786835.874916,2389337.1672882],[11783924.2106,2389337.1672882]]]}},{"type":"Feature","id":15,"properties":{"row":3,"col":4},"geometry":{"type":"Polygon","coordinates":[[[11786835.874916,2389337.1672882],[11786835.874916,2392879.4999952],[11789747.539231,2392879.4999952],[11789747.539231,2389337.1672882],[11786835.874916,2389337.1672882]]]}},{"type":"Feature","id":16,"properties":{"row":4,"col":1},"geometry":{"type":"Polygon","coordinates":[[[11778100.88197,2385794.8345812],[11778100.88197,2389337.1672882],[11781012.546285,2389337.1672882],[11781012.546285,2385794.8345812],[11778100.88197,2385794.8345812]]]}},{"type":"Feature","id":17,"properties":{"row":4,"col":2},"geometry":{"type":"Polygon","coordinates":[[[11781012.546285,2385794.8345812],[11781012.546285,2389337.1672882],[11783924.2106,2389337.1672882],[11783924.2106,2385794.8345812],[11781012.546285,2385794.8345812]]]}},{"type":"Feature","id":18,"properties":{"row":4,"col":3},"geometry":{"type":"Polygon","coordinates":[[[11783924.2106,2385794.8345812],[11783924.2106,2389337.1672882],[11786835.874916,2389337.1672882],[11786835.874916,2385794.8345812],[11783924.2106,2385794.8345812]]]}},{"type":"Feature","id":19,"properties":{"row":4,"col":4},"geometry":{"type":"Polygon","coordinates":[[[11786835.874916,2385794.8345812],[11786835.874916,2389337.1672882],[11789747.539231,2389337.1672882],[11789747.539231,2385794.8345812],[11786835.874916,2385794.8345812]]]}}]}');
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

        $transformedBoundingBox = $this->geoTools->transformBoundingBox($transformedBoundingBox, $targetSrid);
        $this->assertTrue($transformedBoundingBox instanceof BoundingBox);
        $this->assertEquals(105.75218379342, $transformedBoundingBox->getXMin());
        $this->assertEquals(105.91170436595, $transformedBoundingBox->getXMax());
        $this->assertEquals(20.942793923555, $transformedBoundingBox->getYMin());
        $this->assertEquals(21.100124603334, $transformedBoundingBox->getYMax());
        $this->assertEquals($targetSrid, $transformedBoundingBox->getSrid());
    }

    public function testGetGridCellFromPoint()
    {
        $bb = $this->geoTools->transformBoundingBox(new BoundingBox(578205, 594692, 2316000, 2333500, 32648), 4326);
        $gz = new GridSize(165, 175);
        $point = new Point(105.81165, 21, 4326);
        $result = $this->geoTools->getGridCellFromPoint($bb, $gz, $point);
        $this->assertArrayHasKey("row", $result);
        $this->assertArrayHasKey("col", $result);
        $this->assertEquals(111, $result["row"]);
        $this->assertEquals(61, $result["col"]);
    }

    public function testGetActiveCellsPoint()
    {
        $bb = $this->geoTools->transformBoundingBox(new BoundingBox(578205, 594692, 2316000, 2333500, 32648), 4326);
        $gz = new GridSize(165, 175);
        $point = new Point(105.81165, 21, 4326);

        $expected = array();
        $expected[111][61] = true;

        $result = $this->geoTools->getActiveCellsFromPoint($bb, $gz, $point);
        $this->assertInstanceOf(ActiveCells::class, $result);
        $this->assertEquals($expected, $result->toArray());
    }

    public function testReturnNullIfPointIsOutsieOfBoundingBox()
    {
        $bb = $this->geoTools->transformBoundingBox(new BoundingBox(578205, 594692, 2316000, 2333500, 32648), 4326);
        $gz = new GridSize(165, 175);
        $point = new Point(100.81165, 21, 4326);
        $result = $this->geoTools->getGridCellFromPoint($bb, $gz, $point);
        $this->assertEquals(null, $result);
    }

    public function tearDown()
    {
        $area = $this->entityManager->getRepository('AppBundle:Area')
            ->findOneBy(array(
                'id' => $this->area->getId()->toString()
            ));

        $this->entityManager->remove($area);

        $river = $this->entityManager->getRepository('AppBundle:StreamBoundary')
            ->findOneBy(array(
                'id' => $this->river->getId()->toString()
            ));

        $this->entityManager->remove($river);
        $this->entityManager->flush();
    }
}