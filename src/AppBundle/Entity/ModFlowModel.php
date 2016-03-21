<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @JMS\ExclusionPolicy("all")
 */
class ModFlowModel extends AbstractModel
{
    /**
     * @var SoilModel $soilModel
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SoilModel", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="soil_model_id", referencedColumnName="id", onDelete="SET NULL")
     * @JMS\Type("AppBundle\Entity\SoilModel")
     **/
    private $soilModel;

    /**
     * @var Area
     *
     * @JMS\Type("AppBundle\Entity\Area")
     **/
    private $area;

    /**
     * @var ArrayCollection Boundary
     *
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Boundary>")
     **/
    private $boundaries;

    /**
     * @var ArrayCollection
     *
     * @JMS\Type("ArrayCollection<AppBundle\Entity\ObservationPoint>")
     */
    private $observationPoints;

    /**
     * @var ArrayCollection
     *
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Stream>")
     */
    private $streams;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\StressPeriod", mappedBy="model")
     */
    private $stressPeriods;

    /**
     * @var
     *
     * @ORM\Column(type="json_array")
     */
    private $calculationProperties;

    /**
     * ModFlowModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->boundaries = new ArrayCollection();
        $this->observationPoints = new ArrayCollection();
        $this->streams = new ArrayCollection();
        $this->stressPeriods = new ArrayCollection();
    }

    /**
     * @return Area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param Area $area
     * @return ModFlowModel
     */
    public function setArea(Area $area)
    {
        $this->area = $area;
        return $this;
    }

    /**
     * Set soilModel
     *
     * @param SoilModel $soilModel
     *
     * @return ModFlowModel
     */
    public function setSoilModel(SoilModel $soilModel = null)
    {
        $this->soilModel = $soilModel;

        return $this;
    }

    /**
     * Get soilModel
     *
     * @return \AppBundle\Entity\SoilModel
     */
    public function getSoilModel()
    {
        return $this->soilModel;
    }

    /**
     * Get boundaries
     *
     * @return Boundary|ArrayCollection
     */
    public function getBoundaries()
    {
        return $this->boundaries;
    }

    /**
     * Add boundary
     *
     * @param Boundary $boundary
     * @return $this
     */
    public function addBoundary(Boundary $boundary)
    {
        $this->boundaries[] = $boundary;

        return $this;
    }

    /**
     * Remove boundary
     *
     * @param Boundary $boundary
     */
    public function removeBoundary(Boundary $boundary)
    {
        $this->boundaries->removeElement($boundary);
    }

    /**
     * @return ArrayCollection
     */
    public function getObservationPoints()
    {
        return $this->observationPoints;
    }

    /**
     * @param ObservationPoint $observationPoint
     * @return $this
     */
    public function addObservationPoint(ObservationPoint $observationPoint)
    {
        $this->observationPoints[] = $observationPoint;#

        return $this;
    }

    /**
     * @param ObservationPoint $observationPoint
     */
    public function removeObservationPoint(ObservationPoint $observationPoint)
    {
        $this->observationPoints->removeElement($observationPoint);
    }

    /**
     * @return ArrayCollection
     */
    public function getStreams()
    {
        return $this->streams;
    }

    /**
     * @param Stream $stream
     * @return $this
     */
    public function addStream(Stream $stream)
    {
        $this->streams[] = $stream;

        return $this;
    }

    /**
     * @param Stream $stream
     */
    public function removeStream(Stream $stream)
    {
        $this->streams->removeElement($stream);
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        if (!is_null($this->area))
        {
            $this->addModelObject($this->area);
        }

        if ($this->boundaries->count() > 0 )
        {
            foreach ($this->boundaries as $boundary)
            {
                $this->addModelObject($boundary);
            }
        }

        if ($this->observationPoints->count() > 0 )
        {
            foreach ($this->observationPoints as $observationPoint)
            {
                $this->addModelObject($observationPoint);
            }
        }

        if ($this->streams->count() > 0 )
        {
            foreach ($this->streams as $stream)
            {
                $this->addModelObject($stream);
            }
        }
    }

    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        foreach ($this->getModelObjects() as $modelObject)
        {
            if ($modelObject instanceof Area)
            {
                $this->area = $modelObject;
                $this->removeModelObject($modelObject);
            }

            if ($modelObject instanceof Boundary)
            {
                $this->addBoundary($modelObject);
                $this->removeModelObject($modelObject);
            }

            if ($modelObject instanceof ObservationPoint)
            {
                $this->addObservationPoint($modelObject);
                $this->removeModelObject($modelObject);
            }

            if ($modelObject instanceof Stream)
            {
                $this->addStream($modelObject);
                $this->removeModelObject($modelObject);
            }
        }
    }

    /**
     * Add stressPeriod
     *
     * @param \AppBundle\Entity\StressPeriod $stressPeriod
     *
     * @return ModFlowModel
     */
    public function addStressPeriod(\AppBundle\Entity\StressPeriod $stressPeriod)
    {
        $this->stressPeriods[] = $stressPeriod;

        return $this;
    }

    /**
     * Remove stressPeriod
     *
     * @param \AppBundle\Entity\StressPeriod $stressPeriod
     */
    public function removeStressPeriod(\AppBundle\Entity\StressPeriod $stressPeriod)
    {
        $this->stressPeriods->removeElement($stressPeriod);
    }

    /**
     * Get stressPeriods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStressPeriods()
    {
        return $this->stressPeriods;
    }

    /**
     * Set calculationProperties
     *
     * @param array $calculationProperties
     *
     * @return ModFlowModel
     */
    public function setCalculationProperties($calculationProperties)
    {
        $this->calculationProperties = $calculationProperties;

        return $this;
    }

    /**
     * Get calculationProperties
     *
     * @return array
     */
    public function getCalculationProperties()
    {
        return $this->calculationProperties;
    }
}
