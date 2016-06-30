<?php

namespace Tests\AppBundle\Entity;

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
            ->setBoundingBox(new BoundingBox(0.0005, 0.0007, 0.0010, 0.0015, 4326))
            ->setGridSize(new GridSize(10, 11))
            ->setNoDataVal(-999)
            ->setData(
                array(
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9)
                ))
            ->setDescription('Description')
        ;
    }

    public function testSetNullDescriptionMakesEmptyString()
    {
        $this->raster->setDescription();
        $this->assertEquals("", $this->raster->getDescription());
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
        $this->assertEquals($this->raster->getDescription(), $raster->getDescription());

        $rasters = $this->entityManager
            ->getRepository('AppBundle:Raster')
            ->findAll();

        foreach ($rasters as $raster) {
            $this->entityManager->remove($raster);
            $this->entityManager->flush();
        }
    }

    public function testGetFilteredData()
    {

        $this->assertEquals($this->raster->getData(), $this->raster->getFilteredData(null));

        $filter=
            array(
                array(false, true, true, true, true, true, true, true, true , false),
                array(false, true, true, true, true, true, true, true, true , false),
                array(false, true, true, true, true, true, true, true, true , false),
                array(false, true, true, true, true, true, true, true, true , false),
                array(false, true, true, true, true, true, true, true, true , false),
                array(false, true, true, true, true, true, true, true, true , false),
                array(false, true, true, true, true, true, true, true, true , false),
                array(false, true, true, true, true, true, true, true, true , false),
                array(false, true, true, true, true, true, true, true, true , false),
                array(false, true, true, true, true, true, true, true, true , false),
                array(false, true, true, true, true, true, true, true, true , false)
            );

        $expectedFilteredData=
            array(
                array(Raster::DEFAULT_NO_DATA_VAL,1,2,3,4,5,6,7,8,Raster::DEFAULT_NO_DATA_VAL),
                array(Raster::DEFAULT_NO_DATA_VAL,1,2,3,4,5,6,7,8,Raster::DEFAULT_NO_DATA_VAL),
                array(Raster::DEFAULT_NO_DATA_VAL,1,2,3,4,5,6,7,8,Raster::DEFAULT_NO_DATA_VAL),
                array(Raster::DEFAULT_NO_DATA_VAL,1,2,3,4,5,6,7,8,Raster::DEFAULT_NO_DATA_VAL),
                array(Raster::DEFAULT_NO_DATA_VAL,1,2,3,4,5,6,7,8,Raster::DEFAULT_NO_DATA_VAL),
                array(Raster::DEFAULT_NO_DATA_VAL,1,2,3,4,5,6,7,8,Raster::DEFAULT_NO_DATA_VAL),
                array(Raster::DEFAULT_NO_DATA_VAL,1,2,3,4,5,6,7,8,Raster::DEFAULT_NO_DATA_VAL),
                array(Raster::DEFAULT_NO_DATA_VAL,1,2,3,4,5,6,7,8,Raster::DEFAULT_NO_DATA_VAL),
                array(Raster::DEFAULT_NO_DATA_VAL,1,2,3,4,5,6,7,8,Raster::DEFAULT_NO_DATA_VAL),
                array(Raster::DEFAULT_NO_DATA_VAL,1,2,3,4,5,6,7,8,Raster::DEFAULT_NO_DATA_VAL),
                array(Raster::DEFAULT_NO_DATA_VAL,1,2,3,4,5,6,7,8,Raster::DEFAULT_NO_DATA_VAL)
            );

        $filteredData = $this->raster->getFilteredData($filter);
        $this->assertEquals($expectedFilteredData, $filteredData);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
    }
}
