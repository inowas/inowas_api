<?php

namespace AppBundle\Entity;

use AppBundle\Model\ActiveCells;
use AppBundle\Model\StressPeriod;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy2DArray;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\RchStressPeriod;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\RchStressPeriodData;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 */
class RechargeBoundary extends BoundaryModelObject
{
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    protected $type = 'RCH';

    /**
     * @var Polygon
     *
     * @ORM\Column(name="geometry", type="polygon", nullable=true)
     */
    private $geometry;

    /**
     * @var ArrayCollection
     *
     * @ORM\Column(name="stress_periods", type="rch_stress_periods", nullable=true)
     */
    private $stressPeriods;

    /**
     * @return Polygon
     */
    public function getGeometry()
    {
        return $this->geometry;
    }

    /**
     * @param Polygon $geometry
     * @return $this
     */
    public function setGeometry(Polygon $geometry)
    {
        $this->geometry = $geometry;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getStressPeriods()
    {
        return $this->stressPeriods;
    }

    /**
     * @param RchStressPeriod $sp
     * @return $this
     */
    public function addStressPeriod(RchStressPeriod $sp)
    {
        if (is_null($this->stressPeriods)){
            $this->stressPeriods = new ArrayCollection();
        }

        $this->stressPeriods->add($sp);
        return $this;
    }
    
    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("geometry")
     * @JMS\Groups({"modelobjectdetails"})
     *
     * @return string
     */
    public function serializeDeserializeGeometry()
    {
        $polygons = null;

        if (!is_null($this->geometry))
        {
            $new = array();
            $polygons = $this->geometry->toArray();

            foreach ($polygons as $polygon)
            {
                $polygon["type"] = $this->geometry->getType();
                $polygon["srid"] = $this->geometry->getSrid();
                $new[] = $polygon;
            }

            unset($polygons);
            $polygons = $new;
        }
        return $polygons;
    }

    /**
     * @param array $stressPeriodData
     * @param ArrayCollection $globalStressPeriods
     * @return array
     */
    public function addStressPeriodData(array $stressPeriodData, ArrayCollection $globalStressPeriods){

        if ($this->stressPeriods == null){
            return $stressPeriodData;
        }

        /** @var RchStressPeriod $stressPeriod */
        foreach ($this->stressPeriods as $stressPeriod){
            /** @var StressPeriod $globalStressPeriod */
            foreach ($globalStressPeriods as $key => $globalStressPeriod){
                if ($stressPeriod->getDateTimeBegin() == $globalStressPeriod->getDateTimeBegin()){

                    if (! isset($stressPeriodData[$key])){
                        $stressPeriodData[$key] = array();
                    }

                    $stressPeriodData[$key] = RchStressPeriodData::create($stressPeriod->getRech());

                    break;
                }
            }
        }

        return $stressPeriodData;
    }
}
