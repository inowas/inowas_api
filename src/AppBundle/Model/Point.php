<?php

namespace AppBundle\Model;

use JMS\Serializer\Annotation as JMS;

class Point extends \CrEOF\Spatial\PHP\Types\Geometry\Point implements \JsonSerializable
{
    /**
     * @var float
     *
     * @JMS\Groups({"details", "modelobjectdetails"})
     * @JMS\Type("float")
     */
    protected $x;

    /**
     * @var float
     *
     * @JMS\Groups({"details", "modelobjectdetails"})
     * @JMS\Type("float")
     */
    protected $y;

    /**
     * @var int
     *
     * @JMS\Groups({"details", "modelobjectdetails"})
     * @JMS\Type("integer")
     */
    protected $srid;

    /**
     * @return mixed
     */
    function jsonSerialize()
    {
        return array(
            'x' => $this->x,
            'y' => $this->y,
            'srid' => $this->srid
        );
    }
}
