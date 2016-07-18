<?php

namespace InowasPyprocessingBundle\Tests\Service;

use AppBundle\Entity\Raster;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\RasterFactory;
use InowasPyprocessingBundle\Exception\InvalidArgumentException;
use InowasPyprocessingBundle\Model\GeoImage\GeoImageParameter;
use InowasPyprocessingBundle\Service\GeoImage;
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

    /** @var  GeoImage $geoImageService */
    protected $geoImageService;

    /** @var  GeoImageParameter */
    protected $geoImageParameter;

    protected $geoImageParameterMock;

    /** @var  Raster */
    protected $raster;

    /** @var  array */
    protected $activeCells;

    /** @var  float */
    protected $min;

    /** @var  float */
    protected $max;

    /** @var  string */
    protected $fileFormat;

    /** @var  string */
    protected $colorRelief;

    /** @var  integer */
    protected $targetProjection;


    public function setUp()
    {
        self::bootKernel();

        $this->httpKernel = static::$kernel;
        $this->serializer = static::$kernel->getContainer()->get('serializer');
        $this->geoImageService = static::$kernel->getContainer()->get('inowas.geoimage');


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

        $this->activeCells = array();
        $this->min = 0;
        $this->max = 10;
        $this->fileFormat = "png";
        $this->colorRelief = GeoImage::COLOR_RELIEF_GIST_RAINBOW;
        $this->targetProjection = 4326;
    }

    public function testCreatePngImage(){
        $geoImageParameterMock = $this->createGeoImageParameterMock();
        $this->geoImageService->createImage($geoImageParameterMock);
        $this->assertFileExists($this->httpKernel->getContainer()->getParameter('inowas.geoimage.data_folder').'/'.$this->raster->getId()->toString().'.png');
        $this->assertFileExists($this->geoImageService->getOutputFileName());
    }

    public function testThrowInvalidArgumentExceptionIfFileFormatIsNotAvailable()
    {
        $this->fileFormat = 'foo';
        $this->createGeoImageParameterMock();
        $this->setExpectedException(InvalidArgumentException::class);
        $this->geoImageService->createImage($this->geoImageParameterMock);
    }

    public function testThrowInvalidArgumentExceptionIfColorReliefIsNotAvailable()
    {
        $this->colorRelief = 'foo';
        $this->createGeoImageParameterMock();
        $this->setExpectedException(InvalidArgumentException::class);
        $this->geoImageService->createImage($this->geoImageParameterMock);
    }

    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->httpKernel->getContainer()->getParameter('inowas.geoimage.data_folder').'/'.$this->raster->getId()->toString().'.png');
    }

    private function createGeoImageParameterMock()
    {
        $this->geoImageParameterMock = $this->getMockBuilder(GeoImageParameter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->geoImageParameterMock->method('getRaster')->willReturn($this->raster);
        $this->geoImageParameterMock->method('getActiveCells')->willReturn($this->activeCells);
        $this->geoImageParameterMock->method('getMin')->willReturn($this->min);
        $this->geoImageParameterMock->method('getMax')->willReturn($this->max);
        $this->geoImageParameterMock->method('getColorRelief')->willReturn($this->colorRelief);
        $this->geoImageParameterMock->method('getFileFormat')->willReturn($this->fileFormat);
        $this->geoImageParameterMock->method('getTargetProjection')->willReturn($this->targetProjection);
        return $this->geoImageParameterMock;
    }
}