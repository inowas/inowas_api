<?php

namespace AppBundle\Model;

use JMS\Serializer\Annotation as JMS;

class Point extends \CrEOF\Spatial\PHP\Types\Geometry\Point
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
}