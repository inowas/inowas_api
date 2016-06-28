<?php

namespace AppBundle\Tests\Service;

use AppBundle\Entity\Raster;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\RasterFactory;
use AppBundle\Service\GeoImage;
use AppBundle\Service\PythonProcess;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

class GeoImageTest extends WebTestCase
{
    /** @var  Kernel */
    protected $httpKernel;

    /** @var  Serializer */
    protected $serializer;

    /** @var  GeoImage $geoImage */
    protected $geoImage;

    /** @var  Raster $raster */
    protected $raster;

    public function setUp()
    {
        self::bootKernel();

        $this->httpKernel = static::$kernel;
        $this->serializer = static::$kernel->getContainer()->get('serializer');
        $this->geoImage   = static::$kernel->getContainer()->get('inowas.geoimage');

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
        $this->setExpectedException('AppBundle\Exception\InvalidArgumentException');
        $this->geoImage->createImageFromRaster($this->raster, null, null, null, "png", "unknownColorRelief");
    }

    public function testThrowInvalidArgumentExceptionIfImageTypeIsAvailable()
    {
        $this->setExpectedException('AppBundle\Exception\InvalidArgumentException');
        $this->geoImage->createImageFromRaster($this->raster, null, null, null, "kml");
    }

    public function testThrowsExceptionIfProcessIsNotSuccessful()
    {
        $processStub = $this->getMockBuilder(PythonProcess::class)
            ->disableOriginalConstructor()
            ->setMethods(array('setArguments', 'setWorkingDirectory', 'getProcess', 'isSuccessful', 'run'))
            ->getMock()
        ;
        $processStub->method('isSuccessful')->willReturn(false);
        $processStub->method('setArguments')->willReturn($processStub);
        $processStub->method('setWorkingDirectory')->willReturn($processStub);
        $processStub->method('getProcess')->willReturn($processStub);

        /** @var PythonProcess $processStub */
        $geoImage = new GeoImage($this->serializer, $this->httpKernel, $processStub, 'wd', 'dd', 'td');

        $this->setExpectedException('AppBundle\Exception\ProcessFailedException');
        $geoImage->createImageFromRaster($this->raster);
    }

    public function testThrowsExceptionIfProcessIsSuccessfulButHasAnError()
    {
        $processStub = $this->getMockBuilder(PythonProcess::class)
            ->disableOriginalConstructor()
            ->setMethods(array('setArguments', 'setWorkingDirectory', 'getProcess', 'isSuccessful', 'run', 'getOutput'))
            ->getMock()
        ;
        $processStub->method('isSuccessful')->willReturn(true);
        $processStub->method('setArguments')->willReturn($processStub);
        $processStub->method('setWorkingDirectory')->willReturn($processStub);
        $processStub->method('getProcess')->willReturn($processStub);
        $processStub->method('getOutput')->willReturn('{"error":"Exception raised in calculation of method gaussian"}');

        /** @var PythonProcess $processStub */
        $geoImage = new GeoImage($this->serializer, $this->httpKernel, $processStub, 'wd', 'dd', 'td');

        $this->setExpectedException('AppBundle\Exception\ImageGenerationException');
        $geoImage->createImageFromRaster($this->raster);
    }

    public function testProcessIsSuccessfulAndReturnsStdOut()
    {
        $processStub = $this->getMockBuilder(PythonProcess::class)
            ->disableOriginalConstructor()
            ->setMethods(array('setArguments', 'setWorkingDirectory', 'getProcess', 'isSuccessful', 'run', 'getOutput'))
            ->getMock()
        ;
        $processStub->method('isSuccessful')->willReturn(true);
        $processStub->method('setArguments')->willReturn($processStub);
        $processStub->method('setWorkingDirectory')->willReturn($processStub);
        $processStub->method('getProcess')->willReturn($processStub);
        $processStub->method('getOutput')->willReturn('{"success":"Success"}');

        /** @var PythonProcess $processStub */
        $geoImage = new GeoImage($this->serializer, $this->httpKernel, $processStub, 'wd', 'dd', 'td');
        $geoImage->createImageFromRaster($this->raster);
        $this->assertEquals("Success", $geoImage->getStdOut());
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
        $fs->remove(__DIR__.'/dd');
        $fs->remove(__DIR__.'/wd');
    }
}