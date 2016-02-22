<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Calculation;
use AppBundle\Entity\Raster;
use AppBundle\Model\RasterBandFactory;
use AppBundle\Model\RasterFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RasterTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $doctrine;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var Calculation $project
     */
    protected $calculation;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->doctrine = static::$kernel->getContainer()
            ->get('doctrine');

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    public function testConversionWithPlainSQL()
    {
        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->conversionTestPlainSQL();

        $this->assertTrue($result);
    }

    public function testConversionWithDQL()
    {
        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->conversionTestDQL('st_geomfromtext(\'POINT (12.9 50.8)\', 4326)');
        $this->assertContains($result[0][1], "0101000020E6100000CDCCCCCCCCCC29406666666666664940");
    }

    /**
     *
     */
    public function testCreateEmptyRaster()
    {
        $rasterObj = RasterFactory::createModel();
        $rasterObj->setWidth(10);
        $rasterObj->setHeight(10);
        $rasterObj->setUpperLeftX(0.0005);
        $rasterObj->setUpperLeftY(0.0005);
        $rasterObj->setScaleX(1);
        $rasterObj->setScaleY(1);
        $rasterObj->setSkewX(0);
        $rasterObj->setSkewY(0);
        $rasterObj->setSrid(4326);

        $rasterEnt = RasterFactory::createEntity();
        $rasterEnt->setRaster($rasterObj);

        $this->entityManager->persist($rasterEnt);
        $this->entityManager->flush();

        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->setEmptyRaster($rasterEnt);

        $this->assertTrue($result);
    }

    /**
     *
     */
    public function testCreateEmptyRasterAndAddBandWithValues()
    {
        $rasterObj = RasterFactory::createModel();
        $rasterObj->setWidth(10);
        $rasterObj->setHeight(10);
        $rasterObj->setUpperLeftX(0.0005);
        $rasterObj->setUpperLeftY(0.0005);
        $rasterObj->setScaleX(1);
        $rasterObj->setScaleY(1);
        $rasterObj->setSkewX(0);
        $rasterObj->setSkewY(0);
        $rasterObj->setSrid(4326);

        $rasterBand = RasterBandFactory::create();
        $rasterBand->setPixelType('\'32BF\'::text');
        $rasterBand->setInitValue(200);
        $rasterBand->setNoDataVal(-9999);
        $rasterBand->setData(array(
            array(10,1,2,3,4,5,6,7,8,19),
            array(10,1,2,3,4,5,6,7,8,19),
            array(10,1,2,3,4,5,6,7,8,19),
            array(10,1,2,3,4,5,6,7,8,19),
            array(10,1,2,3,4,5,6,7,8,19),
            array(10,1,2,3,4,5,6,7,8,19),
            array(10,1,2,3,4,5,6,7,8,19),
            array(10,1,2,3,4,5,6,7,8,19),
            array(10,1,2,3,4,5,6,7,8,19),
            array(10,1,2,3,4,5,6,7,8,19)
        ));
        $rasterObj->setBand($rasterBand);

        $rasterEntity = RasterFactory::createEntity();
        $rasterEntity->setRaster($rasterObj);

        $this->entityManager->persist($rasterEntity);
        $this->entityManager->flush();

        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->addDataToRaster($rasterEntity);

        $this->assertTrue($result);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
    }
}
