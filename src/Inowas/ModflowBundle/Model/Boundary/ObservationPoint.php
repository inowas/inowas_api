<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\Common\Collections\ArrayCollection;
use Inowas\ModflowBundle\Model\StressPeriodInterface;
use Ramsey\Uuid\Uuid;

class ObservationPoint
{
    /** @var Uuid */
    private $id;

    /** @var Point */
    private $geometry;

    /** @var ArrayCollection */
    private $stressPeriods;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->stressPeriods = new ArrayCollection();
    }

    /**
     * Set point
     *
     * @param point $geometry
     * @return $this
     */
    public function setGeometry(Point $geometry)
    {
        $this->geometry = $geometry;
        return $this;
    }

    /**
     * Get point
     *
     * @return point
     */
    public function getGeometry()
    {
        return $this->geometry;
    }

    /**
     * @return ArrayCollection
     */
    public function getStressPeriods(): ArrayCollection
    {
        return $this->stressPeriods;
    }

    /**
     * @param StressPeriodInterface $stressPeriod
     * @return $this
     */
    public function addStressPeriod(StressPeriodInterface $stressPeriod)
    {
        $this->stressPeriods->add($stressPeriod);
        return $this;
    }
}