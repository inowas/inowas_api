<?php

namespace AppBundle\Model;

use JMS\Serializer\Annotation as JMS;

/**
 * Raster
 */
class Raster
{
    /**
     * @var Raster $raster
     */
    private $raster;

    /**
     * @var integer
     * @JMS\Type("integer")
     */
    private $width;

    /**
     * @var integer
     * @JMS\Type("integer")
     */
    private $height;

    /**
     * @var float
     * @JMS\Type("float")
     */
    private $upperLeftX;

    /**
     * @var float
     * @JMS\Type("float")
     */
    private $upperLeftY;

    /**
     * @var float
     * @JMS\Type("float")
     */
    private $scaleX;

    /**
     * @var float
     * @JMS\Type("float")
     */
    private $scaleY;

    /**
     * @var float
     * @JMS\Type("float")
     */
    private $skewX;

    /**
     * @var float
     * @JMS\Type("float")
     */
    private $skewY;

    /**
     * @var float
     * @JMS\Type("float")
     */
    private $pixelSize;

    /**
     * @var integer
     * @JMS\Type("integer")
     */
    private $srid;

    /**
     * @var RasterBand
     * @JMS\Type("AppBundle\Model\RasterBand")
     */
    private $band;

    /**
     * Raster constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return Raster
     */
    public function getRaster()
    {
        return $this->raster;
    }

    /**
     * @param Raster $raster
     * @return Raster
     */
    public function setRaster($raster)
    {
        $this->raster = $raster;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     * @return Raster
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     * @return Raster
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return float
     */
    public function getUpperLeftX()
    {
        return $this->upperLeftX;
    }

    /**
     * @param float $upperLeftX
     * @return Raster
     */
    public function setUpperLeftX($upperLeftX)
    {
        $this->upperLeftX = $upperLeftX;
        return $this;
    }

    /**
     * @return float
     */
    public function getUpperLeftY()
    {
        return $this->upperLeftY;
    }

    /**
     * @param float $upperLeftY
     * @return Raster
     */
    public function setUpperLeftY($upperLeftY)
    {
        $this->upperLeftY = $upperLeftY;
        return $this;
    }

    /**
     * @return float
     */
    public function getScaleX()
    {
        return $this->scaleX;
    }

    /**
     * @param float $scaleX
     * @return Raster
     */
    public function setScaleX($scaleX)
    {
        $this->scaleX = $scaleX;
        return $this;
    }

    /**
     * @return float
     */
    public function getScaleY()
    {
        return $this->scaleY;
    }

    /**
     * @param float $scaleY
     * @return Raster
     */
    public function setScaleY($scaleY)
    {
        $this->scaleY = $scaleY;
        return $this;
    }

    /**
     * @return float
     */
    public function getSkewX()
    {
        return $this->skewX;
    }

    /**
     * @param float $skewX
     * @return Raster
     */
    public function setSkewX($skewX)
    {
        $this->skewX = $skewX;
        return $this;
    }

    /**
     * @return float
     */
    public function getSkewY()
    {
        return $this->skewY;
    }

    /**
     * @param float $skewY
     * @return Raster
     */
    public function setSkewY($skewY)
    {
        $this->skewY = $skewY;
        return $this;
    }

    /**
     * @return float
     */
    public function getPixelSize()
    {
        return $this->pixelSize;
    }

    /**
     * @param float $pixelSize
     * @return Raster
     */
    public function setPixelSize($pixelSize)
    {
        $this->pixelSize = $pixelSize;
        return $this;
    }

    /**
     * @return int
     */
    public function getSrid()
    {
        return $this->srid;
    }

    /**
     * @param int $srid
     * @return Raster
     */
    public function setSrid($srid)
    {
        $this->srid = $srid;
        return $this;
    }

    /**
     * @return RasterBand
     */
    public function getBand()
    {
        return $this->band;
    }

    /**
     * @param RasterBand $band
     * @return Raster
     */
    public function setBand(RasterBand $band)
    {
        $this->band = $band;
        return $this;
    }
}
