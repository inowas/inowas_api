<?php

namespace AppBundle\Entity;

use AppBundle\Model\Point;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="geological_points")
 */
class GeologicalPoint extends ModelObject
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\GeologicalUnit", mappedBy="geologicalPoint", cascade={"persist"})
     * @JMS\MaxDepth(2)
     * @JMS\Groups({"details", "modelobjectdetails", "soilmodeldetails"})
     */
    private $geologicalUnits;

    /**
     * SoilProfile constructor.
     * @param User|null $owner
     * @param Project|null $project
     * @param bool|false $public
     */
    public function __construct(User $owner = null, Project $project = null, $public = false)
    {
        parent::__construct($owner, $project, $public);
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
        $this->geologicalUnits[] = $geologicalUnit;
        return $this;
    }

    /**
     * Remove geologicalUnit
     *
     * @param \AppBundle\Entity\GeologicalUnit $geologicalUnit
     */
    public function removeRemoveGeologicalUnit(GeologicalUnit $geologicalUnit)
    {
        $this->geologicalUnits->removeElement($geologicalUnit);
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
     * Remove geologicalUnits
     *
     * @param \AppBundle\Entity\GeologicalUnit $geologicalUnits
     */
    public function removeGeologicalUnit(GeologicalUnit $geologicalUnits)
    {
        $this->geologicalUnits->removeElement($geologicalUnits);
    }
}
