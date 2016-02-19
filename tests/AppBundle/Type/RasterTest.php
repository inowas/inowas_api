<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Calculation;
use AppBundle\Entity\Raster;
use AppBundle\Model\RasterFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RasterTest extends WebTestCase
{
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
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    public function testConversionWithPlainSQL()
    {

        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->conversionTestPlainSQL();

    }

    public function testConversionWithDQL()
    {

        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->conversionTestDQL();

    }

    /**
     *
     */
    public function testCreateEmptyRaster()
    {
        $raster = RasterFactory::create();

        $raster->setWidth(100);
        $raster->setHeight(100);
        $raster->setUpperLeftX(0.0005);
        $raster->setUpperLeftY(0.0005);
        $raster->setScaleX(1);
        $raster->setScaleY(1);
        $raster->setSkewX(0);
        $raster->setSkewY(0);
        $raster->setSrid(4326);

        $this->entityManager->persist($raster);
        $this->entityManager->flush();

        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->setEmptyRaster($raster);

    }

    /**
     *
     */
    public function testCreateEmptyRasterAndAddBand()
    {
        $raster = RasterFactory::create();

        $raster->setWidth(100);
        $raster->setHeight(100);
        $raster->setUpperLeftX(0.0005);
        $raster->setUpperLeftY(0.0005);
        $raster->setScaleX(1);
        $raster->setScaleY(1);
        $raster->setSkewX(0);
        $raster->setSkewY(0);
        $raster->setSrid(4326);

        $raster->setBandPixelType('\'8BUI\'::text');
        $raster->setBandInitValue(200);
        $raster->setBandNoDataVal(-9999);

        $this->entityManager->persist($raster);
        $this->entityManager->flush();

        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->setEmptyRasterAndAddBand($raster);
    }

    /**
     *
     */
    public function testCreateEmptyRasterAndAddBandAndValues()
    {
        $raster = RasterFactory::create();
        $raster->setWidth(10);
        $raster->setHeight(10);
        $raster->setUpperLeftX(0.0005);
        $raster->setUpperLeftY(0.0005);
        $raster->setScaleX(1);
        $raster->setScaleY(1);
        $raster->setSkewX(0);
        $raster->setSkewY(0);
        $raster->setSrid(4326);
        $raster->setSimpleRaster(array(
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

        $raster->setBandPixelType('\'8BUI\'::text');
        $raster->setBandInitValue(200);
        $raster->setBandNoDataVal(-9999);

        $this->entityManager->persist($raster);
        $this->entityManager->flush();

        $result = $this->entityManager->getRepository('AppBundle:Raster')
            ->addSimpleRasterToRaster($raster, 1);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
    }
}
