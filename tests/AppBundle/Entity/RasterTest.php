<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Raster;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
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
            ->setBoundingBox(new BoundingBox(0.0005, 0.0007, 0.0010, 0.0015))
            ->setGridSize(new GridSize(10, 11))
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
        $this->assertEquals($this->raster->getBoundingBox(), $raster->getBoundingBox());
        $this->assertEquals($this->raster->getNoDataVal(), $raster->getNoDataVal());
        $this->assertEquals($this->raster->getGridSize(), $raster->getGridSize());
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
