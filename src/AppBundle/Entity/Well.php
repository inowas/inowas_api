<?php

namespace AppBundle\Entity;

use AppBundle\Model\Point;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @ORM\Table(name="wells")
 */
class Well extends BoundaryModelObject
{
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    protected $type = 'well';

    /**
     * @var Point
     *
     * @ORM\Column(name="geometry", type="point", nullable=true)
     */
    private $point;


    /**
     * Set point
     *
     * @param point $point
     * @return $this
     */
    public function setPoint(Point $point)
    {
        $this->point = $point;

        return $this;
    }

    /**
     * Get point
     *
     * @return point
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("point")
     * @JMS\Groups({"details", "modelobjectdetails", "soilmodelobjectdetails"})
     */
    public function convertPointToPoint()
    {
        if (!is_null($this->point))
        {
            $point = new Point($this->point->getX(),$this->point->getY());
            $point->setSrid($this->point->getSrid());
            return $point;
        }

        return null;
    }
}
