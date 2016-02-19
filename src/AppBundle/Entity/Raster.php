<?php
/**
 * More Documentation here:
 *
 * http://postgis.net/docs/manual-dev/RT_ST_MakeEmptyRaster.html
 *
 * raster ST_MakeEmptyRaster(raster rast);
 * raster ST_MakeEmptyRaster(integer width, integer height, float8 upperleftx, float8 upperlefty, float8 scalex, float8 scaley, float8 skewx, float8 skewy, integer srid=unknown);
 * raster ST_MakeEmptyRaster(integer width, integer height, float8 upperleftx, float8 upperlefty, float8 pixelsize);
 *
 *
 * http://postgis.net/docs/manual-2.2/RT_ST_AddBand.html
 *
 *
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Raster
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\RasterRepository")
 * @ORM\Table(name="rasters")
 */
class Raster
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     */
    private $id;

    /**
     * @var array
     *
     * @ORM\Column(name="simple_raster", type="simple_raster", nullable=true)
     */
    private $simpleRaster;

    /**
     * @var
     *
     * @ORM\Column(name="rast", type="raster", nullable=true)
     */
    private $rast;

    /**
     * @var integer
     *
     * @ORM\Column(name="width", type="integer", nullable=true)
     */
    private $width;

    /**
     * @var integer
     *
     * @ORM\Column(name="height", type="integer", nullable=true)
     */
    private $height;

    /**
     * @var float
     *
     * @ORM\Column(name="upper_left_x", type="float", nullable=true)
     */
    private $upperLeftX;

    /**
     * @var float
     *
     * @ORM\Column(name="upper_left_y", type="float", nullable=true)
     */
    private $upperLeftY;

    /**
     * @var float
     *
     * @ORM\Column(name="scale_x", type="float", nullable=true)
     */
    private $scaleX;

    /**
     * @var float
     *
     * @ORM\Column(name="scale_y", type="float", nullable=true)
     */
    private $scaleY;

    /**
     * @var float
     *
     * @ORM\Column(name="skew_x", type="float", nullable=true)
     */
    private $skewX;

    /**
     * @var float
     *
     * @ORM\Column(name="skew_y", type="float", nullable=true)
     */
    private $skewY;

    /**
     * @var float
     *
     * @ORM\Column(name="pixelsize", type="float", nullable=true)
     */
    private $pixelsize;

    /**
     * @var
     *
     * @ORM\Column(name="srid", type="integer", nullable=true)
     */
    private $srid;

    /**
     * @var string
     *
     * @ORM\Column(name="band_pixel_type", type="string", length=255, nullable=true)
     */
    private $bandPixelType;

    /**
     * @var float
     *
     * @ORM\Column(name="initvalue", type="float", nullable=true)
     */
    private $bandInitValue;

    /**
     * @var float
     *
     * @ORM\Column(name="no_data_val", type="float", nullable=true)
     */
    private $bandNoDataVal;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set simpleRaster
     *
     * @param array $simpleRaster
     *
     * @return Raster
     */
    public function setSimpleRaster($simpleRaster)
    {
        $this->simpleRaster = $simpleRaster;

        return $this;
    }

    /**
     * Get simpleRaster
     *
     * @return array
     */
    public function getSimpleRaster()
    {
        return $this->simpleRaster;
    }

    /**
     * Set rast
     *
     * @param raster $rast
     *
     * @return Raster
     */
    public function setRast($rast)
    {
        $this->rast = $rast;

        return $this;
    }

    /**
     * Get rast
     *
     * @return raster
     */
    public function getRast()
    {
        return $this->rast;
    }

    /**
     * Set width
     *
     * @param integer $width
     *
     * @return Raster
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     *
     * @return Raster
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set upperLeftX
     *
     * @param float $upperLeftX
     *
     * @return Raster
     */
    public function setUpperLeftX($upperLeftX)
    {
        $this->upperLeftX = $upperLeftX;

        return $this;
    }

    /**
     * Get upperLeftX
     *
     * @return float
     */
    public function getUpperLeftX()
    {
        return $this->upperLeftX;
    }

    /**
     * Set upperLeftY
     *
     * @param float $upperLeftY
     *
     * @return Raster
     */
    public function setUpperLeftY($upperLeftY)
    {
        $this->upperLeftY = $upperLeftY;

        return $this;
    }

    /**
     * Get upperLeftY
     *
     * @return float
     */
    public function getUpperLeftY()
    {
        return $this->upperLeftY;
    }

    /**
     * Set scaleX
     *
     * @param float $scaleX
     *
     * @return Raster
     */
    public function setScaleX($scaleX)
    {
        $this->scaleX = $scaleX;

        return $this;
    }

    /**
     * Get scaleX
     *
     * @return float
     */
    public function getScaleX()
    {
        return $this->scaleX;
    }

    /**
     * Set scaleY
     *
     * @param float $scaleY
     *
     * @return Raster
     */
    public function setScaleY($scaleY)
    {
        $this->scaleY = $scaleY;

        return $this;
    }

    /**
     * Get scaleY
     *
     * @return float
     */
    public function getScaleY()
    {
        return $this->scaleY;
    }

    /**
     * Set skewX
     *
     * @param float $skewX
     *
     * @return Raster
     */
    public function setSkewX($skewX)
    {
        $this->skewX = $skewX;

        return $this;
    }

    /**
     * Get skewX
     *
     * @return float
     */
    public function getSkewX()
    {
        return $this->skewX;
    }

    /**
     * Set skewY
     *
     * @param float $skewY
     *
     * @return Raster
     */
    public function setSkewY($skewY)
    {
        $this->skewY = $skewY;

        return $this;
    }

    /**
     * Get skewY
     *
     * @return float
     */
    public function getSkewY()
    {
        return $this->skewY;
    }

    /**
     * Set pixelsize
     *
     * @param float $pixelsize
     *
     * @return Raster
     */
    public function setPixelsize($pixelsize)
    {
        $this->pixelsize = $pixelsize;

        return $this;
    }

    /**
     * Get pixelsize
     *
     * @return float
     */
    public function getPixelsize()
    {
        return $this->pixelsize;
    }

    /**
     * Set srid
     *
     * @param integer $srid
     *
     * @return Raster
     */
    public function setSrid($srid)
    {
        $this->srid = $srid;

        return $this;
    }

    /**
     * Get srid
     *
     * @return integer
     */
    public function getSrid()
    {
        return $this->srid;
    }

    /**
     * Set bandPixelType
     *
     * @param string $bandPixelType
     *
     * @return Raster
     */
    public function setBandPixelType($bandPixelType)
    {
        $this->bandPixelType = $bandPixelType;

        return $this;
    }

    /**
     * Get bandPixelType
     *
     * @return string
     */
    public function getBandPixelType()
    {
        return $this->bandPixelType;
    }

    /**
     * Set bandInitValue
     *
     * @param float $bandInitValue
     *
     * @return Raster
     */
    public function setBandInitValue($bandInitValue)
    {
        $this->bandInitValue = $bandInitValue;

        return $this;
    }

    /**
     * Get bandInitValue
     *
     * @return float
     */
    public function getBandInitValue()
    {
        return $this->bandInitValue;
    }

    /**
     * Set bandNoDataVal
     *
     * @param float $bandNoDataVal
     *
     * @return Raster
     */
    public function setBandNoDataVal($bandNoDataVal)
    {
        $this->bandNoDataVal = $bandNoDataVal;

        return $this;
    }

    /**
     * Get bandNoDataVal
     *
     * @return float
     */
    public function getBandNoDataVal()
    {
        return $this->bandNoDataVal;
    }
}
