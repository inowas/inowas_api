<?php

namespace Inowas\Soilmodel\Model;

use CrEOF\Spatial\PHP\Types\Geometry\Point;

class PointValue implements \JsonSerializable
{

    /** @var Point */
    protected $point;

    /**
     * @var float
     */
    protected $value;

    /**
     * @param Point $point
     * @param $value
     */
    public function __construct(Point $point, $value)
    {
        $this->point = $point;
        $this->value = $value;
    }

    /**
     * @return float
     */
    public function getX()
    {
        return $this->point->getX();
    }

    /**
     * @return float
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
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return array(
            'x' => $this->point->getX(),
            'y' => $this->point->getY(),
            'value' => $this->value
        );
    }
}
