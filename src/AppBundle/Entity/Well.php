<?php

namespace AppBundle\Entity;

use AppBundle\Model\Point;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WellRepository")
 * @ORM\Table(name="wells")
 **/
class Well extends BoundaryModelObject
{
    const TYPE_PRIVATE_WELL = "pw";
    const TYPE_INDUSTRIAL_WELL = "iw";

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    protected $type = 'WEL';

    /**
     * @var string
     * @ORM\Column(name="well_type", type="string", length=10)
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    protected $wellType = 'cw';

    /**
     * @var Point
     *
     * @ORM\Column(name="geometry", type="point", nullable=true)
     */
    private $point;

    /**
     * @var GeologicalLayer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\GeologicalLayer")
     */
    private $layer;

    /**
     * @return string
     */
    public function getWellType()
    {
        return $this->wellType;
    }

    /**
     * @param string $wellType
     * @return $this
     */
    public function setWellType($wellType)
    {
        $this->wellType = $wellType;
        return $this;
    }

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
     * @JMS\Groups({"details", "modelobjectdetails"})
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

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("layer")
     * @JMS\Groups({"details", "modelobjectdetails"})
     */
    public function getLayerId()
    {
        if (null === $this->getLayer()){
            return 1;
        }

        /** @var Uuid $id */
        $id = $this->getLayer()->getId();
        return $id->toString();
    }

    /**
     * @return GeologicalLayer
     */
    public function getLayer()
    {
        return $this->layer;
    }

    /**
     * @param $layer
     * @return $this
     */
    public function setLayer($layer)
    {
        $this->layer = $layer;

        return $this;
    }
}
