<?php

namespace AppBundle\Tests\Service;

use AppBundle\Entity\Raster;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\RasterFactory;
use AppBundle\Service\GeoImage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

class GeoImageTest extends WebTestCase
{
    /** @var  GeoImage $geoImage */
    protected $geoImage;

    /** @var  Raster $raster */
    protected $raster;

    public function setUp()
    {
        self::bootKernel();

        $this->geoImage=static::$kernel->getContainer()
            ->get('inowas.geoimage')
        ;

        $this->raster = RasterFactory::create()
            ->setBoundingBox(new BoundingBox(0,10,0,11))
            ->setGridSize(new GridSize(10, 11))
            ->setData(array(
                array(1,2,3,4,5,6,7,8,9,10),
                array(1,2,3,4,5,6,7,8,9,10),
                array(1,2,3,4,5,6,7,8,9,10),
                array(1,2,3,4,5,6,7,8,9,10),
                array(1,2,3,4,5,6,7,8,9,10),
                array(1,2,3,4,5,6,7,8,9,10),
                array(1,2,3,4,5,6,7,8,9,10),
                array(1,2,3,4,5,6,7,8,9,10),
                array(1,2,3,4,5,6,7,8,9,10),
                array(1,2,3,4,5,6,7,8,9,10),
                array(1,2,3,4,5,6,7,8,9,10)
            ));
    }

    public function testThrowInvalidArgumentExceptionIfRastersBoundingBoxIsNull()
    {
        $this->raster->setBoundingBox(null);
        $this->setExpectedException('AppBundle\Exception\InvalidArgumentException');
        $this->geoImage->createImageFromRaster($this->raster);
    }

    public function testThrowInvalidArgumentExceptionIfRastersGridSizeIsNull()
    {
        $this->raster->setGridSize(null);
        $this->setExpectedException('AppBundle\Exception\InvalidArgumentException');
        $this->geoImage->createImageFromRaster($this->raster);
    }

    public function testThrowInvalidArgumentExceptionIfRastersGridSizeYDiffersFromRasterData()
    {
        $gridSize = clone $this->raster->getGridSize();
        $gridSize->setNY($this->raster->getGridSize()->getNY()+1);
        $this->raster->setGridSize($gridSize);
        $this->setExpectedException('AppBundle\Exception\InvalidArgumentException');
        $this->geoImage->createImageFromRaster($this->raster);
    }

    public function testThrowInvalidArgumentExceptionIfRastersGridSizeXDiffersFromRasterData()
    {
        $gridSize = clone $this->raster->getGridSize();
        $gridSize->setNX($this->raster->getGridSize()->getNX()+1);
        $this->raster->setGridSize($gridSize);
        $this->setExpectedException('AppBundle\Exception\InvalidArgumentException');
        $this->geoImage->createImageFromRaster($this->raster);
    }

    public function testThrowInvalidArgumentExceptionIfColorReliefIsNotAvailable()
    {
        $this->raster->setGridSize(null);
        $this->setExpectedException('AppBundle\Exception\InvalidArgumentException');
        $this->geoImage->createImageFromRaster($this->raster, null, null, "png", "unknownColorRelief");
    }

    public function testThrowInvalidArgumentExceptionIfImageTypeIsAvailable()
    {
        $this->raster->setGridSize(null);
        $this->setExpectedException('AppBundle\Exception\InvalidArgumentException');
        $this->geoImage->createImageFromRaster($this->raster, null, null, "kml");
    }

    public function testCreatePng()
    {
        $this->geoImage->createImageFromRaster($this->raster);
        $this->assertFileExists(__DIR__.'/../../data/geotiff/'.$this->raster->getId()->toString().'.png');
    }

    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove(__DIR__.'/../../data/geotiff/'.$this->raster->getId()->toString().'.png');
    }
}