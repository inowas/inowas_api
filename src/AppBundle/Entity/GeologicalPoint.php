<?php

namespace AppBundle\Entity;

use AppBundle\Model\Point;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="geological_points")
 * @ORM\HasLifecycleCallbacks()
 */
class GeologicalPoint extends SoilModelObject
{
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    protected $type = 'geologicalpoint';

    /**
     * @var Point
     *
     * @ORM\Column(name="geometry", type="point", nullable=true)
     * @JMS\Groups({"details", "modelobjectdetails", "soilmodelobjectdetails"})
     */
    private $point;

    /**
     * @var ArrayCollection GeologicalUnit $geologicalUnit
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\GeologicalUnit", cascade={"persist"})
     * @ORM\JoinTable(name="geological_points_geological_units",
     *     joinColumns={@ORM\JoinColumn(name="geological_point_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="geological_unit_id", referencedColumnName="id", unique=true)}
     *     )
     * @JMS\MaxDepth(2)
     * @JMS\Groups({"details", "modelobjectdetails", "soilmodeldetails"})
     */
    private $geologicalUnits;

    /**
     * SoilProfile constructor.
     * @param User|null $owner
     * @param bool|false $public
     */
    public function __construct(User $owner = null, $public = false)
    {
        parent::__construct($owner, $public);
        $this->geologicalUnits = new ArrayCollection();
    }

    /**
     * Set point
     *
     * @param point $point
     * @return GeologicalPoint
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
     * Add geologicalUnit
     *
     * @param \AppBundle\Entity\GeologicalUnit $geologicalUnit
     * @return GeologicalPoint
     */
    public function addGeologicalUnit(GeologicalUnit $geologicalUnit)
    {
        if (!$this->geologicalUnits->contains($geologicalUnit)) {
            if (is_null($geologicalUnit->getOrder())) {
                $geologicalUnit->setOrder($this->getGeologicalUnits()->count());
            }

            $this->geologicalUnits[] = $geologicalUnit;
        }

        return $this;
    }

    /**
     * Remove geologicalUnit
     *
     * @param GeologicalUnit $geologicalUnit
     * @return $this
     */
    public function removeGeologicalUnit(GeologicalUnit $geologicalUnit)
    {
        if ($this->geologicalUnits->contains($geologicalUnit)) {
            $this->geologicalUnits->removeElement($geologicalUnit);
        }
        return $this;
    }

    /**
     * Get geologicalUnits
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGeologicalUnits()
    {
        return $this->geologicalUnits;
    }

    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        /** @var GeologicalUnit $geologicalUnit */
        foreach ($this->geologicalUnits as $geologicalUnit)
        {
            $geologicalUnit->setPoint($this->point);
        }
    }
}
