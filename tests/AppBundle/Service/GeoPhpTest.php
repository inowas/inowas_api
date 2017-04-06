<?php

namespace Tests\AppBundle\Service;


use Doctrine\DBAL\Connection;
use Inowas\Common\Boundaries\AreaBoundary;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\BoundaryId;
use Inowas\GeoToolsBundle\Service\GeoTools;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GeoPhpTest extends WebTestCase
{

    /** @var  AreaBoundary */
    protected $area;

    /** @var  BoundingBox */
    protected $boundingBox;

    /** @var  GridSize $gridSize */
    protected $gridSize;

    /** @var  GeoTools */
    protected $geoTools;

    /** @var  Connection */
    protected $connection;

    public function setUp()
    {
        self::bootKernel();

        $this->geoTools = static::$kernel->getContainer()
            ->get('inowas.geotools.geotools');

        $this->connection = static::$kernel->getContainer()
            ->get('doctrine.dbal.default_connection');

        $this->area = AreaBoundary::create(BoundaryId::generate())
            ->setName(BoundaryName::fromString('Hanoi Area'))
            ->setGeometry(Geometry::fromPolygon(new Polygon(array(
                array(
                    array(105.790767733626808, 21.094425932026443),
                    array(105.796959843400032, 21.093521487879368),
                    array(105.802017060333782, 21.092234483652170),
                    array(105.808084259744490, 21.090442258424751),
                    array(105.812499379361824, 21.088745285770433),
                    array(105.817189857772419, 21.086246452411380),
                    array(105.821849880920155, 21.083084791161816),
                    array(105.826206685192972, 21.080549811906632),
                    array(105.829745666549428, 21.077143263497668),
                    array(105.833738284468225, 21.073871989488410),
                    array(105.837054371969458, 21.068790508713093),
                    array(105.843156477826938, 21.061619066459148),
                    array(105.845257297050807, 21.058494488216656),
                    array(105.848091064693264, 21.055416254106909),
                    array(105.850415052797018, 21.051740212147806),
                    array(105.853986426189834, 21.047219935885728),
                    array(105.857317797743207, 21.042700799256870),
                    array(105.860886165285677, 21.037730164508108),
                    array(105.862781077291359, 21.033668431680731),
                    array(105.865628458812012, 21.028476242159179),
                    array(105.867512713611035, 21.022613568026749),
                    array(105.869402048566840, 21.017651320651229),
                    array(105.871388782041976, 21.013426442220442),
                    array(105.872849945737570, 21.008166192541132),
                    array(105.876181664767913, 21.003946864458868),
                    array(105.882508712001197, 21.001813076331899),
                    array(105.889491767034770, 21.000288452359857),
                    array(105.894324807327010, 20.997811850332017),
                    array(105.898130162725238, 20.994990356212355),
                    array(105.903035574892471, 20.989098851962478),
                    array(105.905619253163707, 20.984707849769400),
                    array(105.905107309855680, 20.977094091795209),
                    array(105.901707144804220, 20.969670720258843),
                    array(105.896052272867848, 20.959195015805960),
                    array(105.886865167028475, 20.950138230157627),
                    array(105.877901274443431, 20.947208019282808),
                    array(105.834499067698161, 20.951978316227517),
                    array(105.806257646336405, 20.968923300374374),
                    array(105.781856978173835, 21.008608549010258),
                    array(105.768216532593982, 21.039487418417067),
                    array(105.774357585691064, 21.072902571997240),
                    array(105.777062025914603, 21.090749775344797),
                    array(105.783049106327312, 21.093961473086512),
                    array(105.790767733626808, 21.094425932026443)
                )
            ), 4326)));

        $this->boundingBox = BoundingBox::fromEPSG4326Coordinates(
            105.75218379342,
            105.91170436595,
            20.942793923555,
            21.100124603334,
            0,
            0
        );

        $this->gridSize = GridSize::fromXY(160, 170);
    }

    public function testCreateWKTFromAreaGeometry() {
        /** @var \Polygon $area */
        $areaPolygon = \geoPHP::load($this->area->geometry()->toJson(), 'json');
        $this->assertInstanceOf(\Polygon::class, $areaPolygon);
    }

    public function testCreateWKTFromBoundingBox() {
        /** @var \Polygon $boundingBox */
        $boundingBoxPolygon = \geoPHP::load($this->boundingBox->toGeoJson(), 'json');
        $this->assertInstanceOf(\Polygon::class, $boundingBoxPolygon);
    }

    public function testMakeAPostGisSqlDisjointQuery(){
        $result = $this->connection->fetchAssoc(
            'SELECT ST_Disjoint(\'POINT(0 0)\'::geometry, \'LINESTRING ( 0 0, 0 2 )\'::geometry);'
        );

        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('st_disjoint', $result));
        $this->assertFalse($result['st_disjoint']);
    }

    public function testPostGisGeomFromText(){
        $areaPolygon = \geoPHP::load($this->area->geometry()->toJson(), 'json');
        $result = $this->connection->fetchAssoc(sprintf('SELECT ST_GeomFromText(\'%s\');', $areaPolygon->asText()));
        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('st_geomfromtext', $result));
    }

    public function testIntersectAreaWithBoundingBox(){
        $areaPolygon = \geoPHP::load($this->area->geometry()->toJson(), 'json');
        $boundingBoxPolygon = \geoPHP::load($this->boundingBox->toGeoJson(), 'json');
        $result = $this->connection->fetchAssoc(sprintf('SELECT ST_Intersects(ST_GeomFromText(\'%s\'),ST_GeomFromText(\'%s\'));', $boundingBoxPolygon->asText(), $areaPolygon->asText()));
        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('st_intersects', $result));
        $this->assertTrue($result['st_intersects']);
    }

    public function test_calculate_active_cells_with_postgis(): void
    {
        $result = $this->geoTools->getActiveCellsFromArea($this->area, $this->boundingBox, $this->gridSize, false);
        $this->assertInstanceOf(ActiveCells::class, $result);
    }

    public function test_integration_if_geos_is_available(): void
    {
        $this->assertTrue(\geoPHP::geosInstalled());
        $areaPolygon = \geoPHP::load($this->area->geometry()->toJson(), 'json');
        $this->assertEquals("GEOSGeometry", get_class($areaPolygon->geos()));
    }

    public function test_geos_point_on_surface(): void
    {
        $x = 105.833738284468225;
        $y = 21.073871989488410;
        $area = \geoPHP::load($this->area->geometry()->toJson(), 'json')->geos();
        $point = \geoPHP::load(sprintf('POINT(%f %f)', $x, $y), 'wkt')->geos();
        $this->assertTrue($area->covers($point));
        $this->assertTrue($point->within($area));
        $this->assertFalse($point->covers($area));
    }

    public function test_calculate_active_cells_with_geos(): void
    {
        $result = $this->geoTools->getActiveCellsFromArea($this->area, $this->boundingBox, $this->gridSize);
        $this->assertInstanceOf(ActiveCells::class, $result);
    }

}
