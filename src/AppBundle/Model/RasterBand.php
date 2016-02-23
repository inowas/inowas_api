<?php

namespace AppBundle\Model;

use JMS\Serializer\Annotation as JMS;

class RasterBand
{
    /**
     * @var float[][]
     * @JMS\Type("array<array>")
     */
    private $data;

    /**
     * @var string
     * @JMS\Type("string")
     */
    private $pixelType;

    /**
     * @var float
     * @JMS\Type("float")
     */
    private $initValue;

    /**
     * @var float
     * @JMS\Type("float")
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
