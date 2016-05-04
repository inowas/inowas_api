<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Raster;
use AppBundle\Model\RasterBandFactory;
use AppBundle\Model\RasterFactory;
use Doctrine\DBAL\Driver\PDOConnection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RasterTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var Raster
     */
    protected $emptyRaster;

    /**
     * @var Raster
     */
    protected $rasterWithData;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        $emptyRasterObject = RasterFactory::createModel();
        $emptyRasterObject
            ->setWidth(10)
            ->setHeight(11)
            ->setUpperLeftX(0.0005)
            ->setUpperLeftY(0.0005)
            ->setScaleX(1)
            ->setScaleY(1)
            ->setSkewX(0)
            ->setSkewy(0)
            ->setSrid(4326);

        $rasterBand = RasterBandFactory::create();
        $rasterBand->setPixelType('\'32BF\'::text');
        $rasterBand->setInitValue(200);
        $rasterBand->setNoDataVal(-9999);
        $rasterBand->setData(array(
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,7,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9)
        ));

        $rasterObjectWithData = clone $emptyRasterObject;
        $rasterObjectWithData->setBand($rasterBand);


        $this->emptyRaster = new Raster();
        $this->emptyRaster->setRaster($emptyRasterObject);

        $this->rasterWithData = new Raster();
        $this->rasterWithData->setRaster($rasterObjectWithData);
    }

    /**
     *
     */
    public function testConversionWithPlainSQL()
    {
        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->executePlainSQL(
                "SELECT st_astext(
                    st_transform(
                        st_geometryfromtext('POINT (12.9 50.8)', 4326), 900913
                )) as test"
            );

        $this->assertContains($result[0]["test"], "POINT(1436021.43123323 6585991.99809962)");
    }

    /**
     * For this test the table has to have some content
     */
    public function testConversionWithDQL()
    {
        $this->entityManager->persist($this->emptyRaster);
        $this->entityManager->flush();

        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->conversionTestDQL('st_geomfromtext(\'POINT (12.9 50.8)\', 4326)');
        $this->assertContains($result[0][1], "0101000020E6100000CDCCCCCCCCCC29406666666666664940");

        $this->entityManager->remove($this->emptyRaster);
        $this->entityManager->flush();
    }

    /**
     *
     */
    public function testCreateEmptyRaster()
    {
        $this->entityManager->persist($this->emptyRaster);
        $this->entityManager->flush();

        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->setEmptyRaster($this->emptyRaster);

        $this->assertTrue($result);

        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->executePlainSQL(
                "
                  SELECT id, (md).*
                  FROM (SELECT id, ST_MetaData(rast) As md
	              FROM rasters
	              WHERE id IN('".$this->emptyRaster->getId()."')) As foo;
                "
            );

        $this->assertEquals($result[0]["id"], $this->emptyRaster->getId());
        $this->assertEquals($result[0]["upperleftx"], 0.0005);
        $this->assertEquals($result[0]["upperlefty"], 0.0005);
        $this->assertEquals($result[0]["width"], 10);
        $this->assertEquals($result[0]["height"], 11);
        $this->assertEquals($result[0]["scalex"], 1);
        $this->assertEquals($result[0]["scaley"], 1);
        $this->assertEquals($result[0]["skewx"], 0);
        $this->assertEquals($result[0]["skewy"], 0);
        $this->assertEquals($result[0]["srid"], 4326);
        $this->assertEquals($result[0]["numbands"], 0);

        $this->entityManager->remove($this->emptyRaster);
        $this->entityManager->flush();
    }

    /**
     *
     */
    public function testCreateEmptyRasterAndAddBandWithValues()
    {
        $this->entityManager->persist($this->rasterWithData);
        $this->entityManager->flush();

        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->addRasterWithData($this->rasterWithData);

        $this->assertTrue($result);

        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->executePlainSQL(
                "
                  SELECT id, (md).*
                  FROM (SELECT id, ST_MetaData(rast) As md
	              FROM rasters
	              WHERE id IN('".$this->rasterWithData->getId()."')) As foo;
                "
            );

        $this->assertEquals($result[0]["numbands"], 1);

        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->executePlainSQL(
                "
                  SELECT  (bmd).*
                  FROM (SELECT ST_BandMetaData(rast, 1) As bmd
                  FROM rasters WHERE id = '".$this->rasterWithData->getId()."') AS foo;
                "
            );

        $this->assertEquals($result[0]["pixeltype"], "32BF");
        $this->assertEquals($result[0]["nodatavalue"], -9999);
        $this->assertEquals($result[0]["isoutdb"], false);
        $this->assertEquals($result[0]["path"], null);

        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->getValuesFromRaster($this->rasterWithData->getId());

        $this->assertEquals($result, $this->rasterWithData->getRaster()->getBand()->getData());

        $this->entityManager->remove($this->rasterWithData);
        $this->entityManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
    }
}
