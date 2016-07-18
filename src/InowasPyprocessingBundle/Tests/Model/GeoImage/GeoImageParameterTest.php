<?php

namespace InowasPyprocessingBundle\Tests\Model\GeoImage;

use AppBundle\Entity\Raster;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\RasterFactory;
use InowasPyprocessingBundle\Exception\InvalidArgumentException;
use InowasPyprocessingBundle\Model\GeoImage\GeoImageParameter;
use InowasPyprocessingBundle\Service\GeoImage;

class GeoImageParameterTest extends \PHPUnit_Framework_TestCase
{

    /** @var Raster $raster **/
    protected $raster;

    /** @var integer */
    protected $activeCells;

    /** @var float */
    protected $min;

    /** @var float */
    protected $max;

    /** @var string */
    protected $fileFormat;

    /** @var string */
    protected $colorRelief;

    /** @var integer */
    protected $targetProjection;

    /** @var  GeoImageParameter */
    protected $geoImageParameter;

    public function setUp(){
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
        $this->min = 123.1;
        $this->max = 124.1;
        $this->fileFormat="tiff";
        $this->colorRelief=GeoImage::COLOR_RELIEF_GIST_RAINBOW;
        $this->targetProjection=4326;
    }

    public function testInstantiate(){
        $this->instantiate();
        $this->assertInstanceOf(GeoImageParameter::class, $this->geoImageParameter);
    }

    public function testGetter(){
        $this->instantiate();
        $this->assertEquals($this->raster, $this->geoImageParameter->getRaster());
        $this->assertEquals($this->activeCells, $this->geoImageParameter->getActiveCells());
        $this->assertEquals($this->min, $this->geoImageParameter->getMin());
        $this->assertEquals($this->max, $this->geoImageParameter->getMax());
        $this->assertEquals($this->fileFormat, $this->geoImageParameter->getFileFormat());
        $this->assertEquals($this->colorRelief, $this->geoImageParameter->getColorRelief());
        $this->assertEquals($this->targetProjection, $this->geoImageParameter->getTargetProjection());
    }

    public function testNotValidBoundingBoxThrowsException(){
        $this->raster->setBoundingBox(null);
        $this->setExpectedException(InvalidArgumentException::class);
        $this->instantiate();
    }

    public function testNotValidGridSizeThrowsException(){
        $this->raster->setGridSize(null);
        $this->setExpectedException(InvalidArgumentException::class);
        $this->instantiate();
    }

    public function testNotCorrespondingGridSizeRowsThrowsException(){
        $this->raster->setGridSize(new GridSize(10, 10));
        $this->setExpectedException(InvalidArgumentException::class);
        $this->instantiate();
    }

    public function testNotCorrespondingGridSizeCellsThrowsException(){
        $this->raster->setGridSize(new GridSize(9, 11));
        $this->setExpectedException(InvalidArgumentException::class);
        $this->instantiate();
    }

    private function instantiate()
    {
        $this->geoImageParameter = new GeoImageParameter(
            $this->raster,
            $this->activeCells,
            $this->min,
            $this->max,
            $this->fileFormat,
            $this->colorRelief,
            $this->targetProjection
        );
    }

}
