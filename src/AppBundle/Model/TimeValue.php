<?php

namespace AppBundle\Model;

use AppBundle\Entity\Raster;
use JMS\Serializer\Annotation as JMS;

class TimeValue implements TimeValueInterface
{
    /**
     * @var \DateTime|null
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails"})
     */
    private $datetime;

    /**
     * @var float
     * @JMS\Type("float")
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails"})
     */
    private $value;

    /**
     * @var Raster
     * @JMS\Groups({"modeldetails", "modelobjectdetails"})
     */
    private $raster;

    /**
     * @return \DateTime|null
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param $datetime
     * @return $this
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
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
     * @return $this
     */
    public function setRaster(Raster $raster = null)
    {
        $this->raster = $raster;
        return $this;
    }
}

