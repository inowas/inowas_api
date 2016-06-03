<?php

namespace AppBundle\Model\Interpolation;

use JMS\Serializer\Annotation as JMS;

class PointValue
{
    /**
     * @var float
     *
     * @JMS\Groups({"interpolation"})
     */
    protected $x;

    /**
     * @var float
     *
     * @JMS\Groups({"interpolation"})
     */
    protected $y;

    /**
     * @var float
     *
     * @JMS\Groups({"interpolation"})
     */
    protected $value;

    /**
     * Point constructor.
     * @param $x
     * @param $y
     * @param $value
     */
    public function __construct($x = null, $y = null, $value = null)
    {
        $this->x = $x;
        $this->y = $y;
        $this->value = $value;
    }

    /**
     * @return null
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @param null $x
     */
    public function setX($x)
    {
        $this->x = $x;
    }

    /**
     * @return null
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @param null $y
     */
    public function setY($y)
    {
        $this->y = $y;
    }

    /**
     * @return null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param null $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    

}