<?php

namespace AppBundle\Model;

class RasterBand
{
    /**
     * @var float[][]
     */
    private $data;

    /**
     * @var string
     */
    private $pixelType;

    /**
     * @var float
     */
    private $initValue;

    /**
     * @var float
     */
    private $noDataVal;

    /**
     * @return integer
     */
    public function getId()
    {
        return 1;
    }

    /**
     * @return \float[][]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \float[][] $data
     * @return RasterBand
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getPixelType()
    {
        return $this->pixelType;
    }

    /**
     * @param string $pixelType
     * @return RasterBand
     */
    public function setPixelType($pixelType)
    {
        $this->pixelType = $pixelType;
        return $this;
    }

    /**
     * @return float
     */
    public function getInitValue()
    {
        return $this->initValue;
    }

    /**
     * @param float $initValue
     * @return RasterBand
     */
    public function setInitValue($initValue)
    {
        $this->initValue = $initValue;
        return $this;
    }

    /**
     * @return float
     */
    public function getNoDataVal()
    {
        return $this->noDataVal;
    }

    /**
     * @param float $noDataVal
     * @return RasterBand
     */
    public function setNoDataVal($noDataVal)
    {
        $this->noDataVal = $noDataVal;
        return $this;
    }
}
