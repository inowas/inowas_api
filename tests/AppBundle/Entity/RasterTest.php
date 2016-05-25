<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Raster;
use AppBundle\Model\RasterFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RasterTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var Raster $raster */
    protected $raster;

    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->raster = RasterFactory::create();
        $this->raster
            ->setUpperLeftX(0.0005)
            ->setUpperLeftY(0.0007)
            ->setLowerRightX(0.010)
            ->setLowerRightY(0.015)
            ->setNumberOfColumns(10)
            ->setNumberOfRows(11)
            ->setNoDataVal(-999)
            ->setSrid(4326)
            ->setData(
                array(
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
            ))
        ;
    }

    public function testStoreRasterInDatabase() {
        $this->entityManager->persist($this->raster);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $raster = $this->entityManager
            ->getRepository('AppBundle:Raster')
            ->findOneBy(array(
                'id' => $this->raster->getId()
            ));

        $this->assertEquals($this->raster->getId(), $raster->getId());
        $this->assertEquals($this->raster->getUpperLeftX(), $raster->getUpperLeftX());
        $this->assertEquals($this->raster->getUpperLeftY(), $raster->getUpperLeftY());
        $this->assertEquals($this->raster->getLowerRightX(), $raster->getLowerRightX());
        $this->assertEquals($this->raster->getLowerRightY(), $raster->getLowerRightY());
        $this->assertEquals($this->raster->getNoDataVal(), $raster->getNoDataVal());
        $this->assertEquals($this->raster->getNumberOfColumns(), $raster->getNumberOfColumns());
        $this->assertEquals($this->raster->getNumberOfRows(), $raster->getNumberOfRows());
        $this->assertEquals($this->raster->getData(), $raster->getData());
        $this->assertEquals($this->raster->getSrid(), $raster->getSrid());

        $rasters = $this->entityManager
            ->getRepository('AppBundle:Raster')
            ->findAll();

        foreach ($rasters as $raster) {
            $this->entityManager->remove($raster);
            $this->entityManager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
    }
}
