<?php

namespace AppBundle\Entity;

use AppBundle\Model\ActiveCells;
use AppBundle\Model\Point;
use AppBundle\Model\StressPeriod;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\WelStressPeriod;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\WelStressPeriodData;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WellRepository")
 * @ORM\Table(name="wells")
 **/
class WellBoundary extends BoundaryModelObject
{
    const TYPE_PRIVATE_WELL = "prw";
    const TYPE_PUBLIC_WELL = "puw";
    const TYPE_OBSERVATION_WELL = "ow";
    const TYPE_INDUSTRIAL_WELL = "iw";
    const TYPE_SCENARIO_MOVED_WELL = "smw";
    const TYPE_SCENARIO_NEW_INDUSTRIAL_WELL = "sniw";
    const TYPE_SCENARIO_NEW_INFILTRATION_WELL = "snifw";
    const TYPE_SCENARIO_NEW_PUBLIC_WELL = "snpw";
    const TYPE_SCENARIO_NEW_WELL = "snw";
    const TYPE_SCENARIO_REMOVED_WELL = "srw";

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
    protected $wellType = self::TYPE_PUBLIC_WELL;

    /**
     * @var Point
     *
     * @ORM\Column(name="geometry", type="point", nullable=true)
     */
    private $geometry;

    /**
     * @var GeologicalLayer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\GeologicalLayer")
     */
    private $layer;

    /**
     * @var ArrayCollection
     *
     * @ORM\Column(name="stress_periods", type="wel_stress_periods", nullable=true)
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    private $stressPeriods;

    /**
     * WellBoundary constructor.
     * @param User $owner
     * @param bool $public
     */
    public function __construct(User $owner = null, $public = false)
    {
        parent::__construct($owner, $public);
        $this->stressPeriods = new ArrayCollection();
    }

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
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("point")
     * @JMS\Groups({"details", "modelobjectdetails"})
     */
    public function convertPointToPoint()
    {
        if (!is_null($this->geometry))
        {
            $point = new Point($this->geometry->getX(),$this->geometry->getY());
            $point->setSrid($this->geometry->getSrid());
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
        if (! $this->layer instanceof GeologicalLayer){
            return null;
        }

        return $this->layer->getId()->toString();
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

    /**
     * @return ArrayCollection
     */
    public function getStressPeriods()
    {
        return $this->stressPeriods;
    }

    /**
     * @param WelStressPeriod $sp
     * @return $this
     */
    public function addStressPeriod(WelStressPeriod $sp)
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

        if (! $stressPeriod instanceof WelStressPeriod){
            throw new InvalidArgumentException(
                'First Argument is supposed to be from Type WelStressPeriod, %s given.', gettype($stressPeriod)
            );
        }

        $stressPeriodData = array();

        foreach ($activeCells->toArray() as $nRow => $row){
            foreach ($row as $nCol => $value){
                if ($value == true){
                    if (is_int($nRow) && is_int($nCol)){
                        $stressPeriodData[] = WelStressPeriodData::create($this->layer->getOrder(), $nRow, $nCol, $stressPeriod->getFlux());
                    }
                }
            }
        }

        return $stressPeriodData;
    }

    /**
     *
     */
    function __clone()
    {
        $this->id = Uuid::uuid4();
    }


}
