<?php

namespace AppBundle\Entity;

use AppBundle\Model\ActiveCells;
use AppBundle\Model\StressPeriod;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\RchStressPeriod;
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

    public function __construct(User $owner = null, $public = true)
    {
        parent::__construct($owner, $public);
        $this->stressPeriods = new ArrayCollection();
    }

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
        $this->stressPeriods->add($sp);
        return $this;
    }

    /**
     * @param StressPeriod $stressPeriod
     * @param ActiveCells $activeCells
     * @return array
     */
    public function generateStressPeriodData(StressPeriod $stressPeriod, ActiveCells $activeCells){

        if (! $stressPeriod instanceof RchStressPeriod){
            throw new InvalidArgumentException(
                'First Argument is supposed to be from Type RchStressPeriod, %s given.', gettype($stressPeriod)
            );
        }

        $stressPeriodData = $stressPeriod->getRech()->toReducedArray();

        return $stressPeriodData;
    }
}
