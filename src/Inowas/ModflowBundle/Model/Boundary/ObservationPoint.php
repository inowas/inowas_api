<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @return Collection
     */
    public function getStressPeriods(): Collection
    {
        return $this->stressPeriods;
    }

    /**
     * @param ArrayCollection $stressPeriods
     * @return ObservationPoint
     */
    public function setStressPeriods(ArrayCollection $stressPeriods): ObservationPoint
    {
        $this->stressPeriods = $stressPeriods;
        return $this;
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


    /**
     * @return null|string
     */
    public function getJsonGeometry(){
        if (! $this->geometry instanceof Point){
            return null;
        }

        return $this->geometry->toJson();
    }
}