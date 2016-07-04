<?php

namespace AppBundle\Model\Interpolation;

use AppBundle\Model\Point;
use JMS\Serializer\Annotation as JMS;

class PointValue
{

    /** @var Point */
    protected $point;

    /**
     * @var float
     *
     * @JMS\Groups({"interpolation"})
     */
    protected $value;

    /**
     * PointValue constructor.
     * @param Point $point
     * @param $value
     */
    public function __construct(Point $point, $value)
    {
        $this->point = $point;
        $this->value = $value;
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("x")
     * @JMS\Groups({"interpolation"})
     * @return null
     */
    public function getX()
    {
        return $this->point->getX();
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("y")
     * @JMS\Groups({"interpolation"})
     * @return null
     */
    public function getY()
    {
        return $this->point->getY();
    }

    /**
     * @return Point
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * @return null
     */
    public function getValue()
    {
        return $this->value;
    }
}