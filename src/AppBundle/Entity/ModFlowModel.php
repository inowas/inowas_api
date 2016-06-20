<?php

namespace AppBundle\Entity;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModFlowModelRepository")
 * @JMS\ExclusionPolicy("none")
 */
class ModFlowModel extends AbstractModel
{
    /**
     * @var ArrayCollection ModelObject $modelObjects
     *
     * @ORM\ManyToMany(targetEntity="ModelObject", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="models_model_objects",
     *      joinColumns={@ORM\JoinColumn(name="model_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="model_object_id", referencedColumnName="id")}
     *      )
     **/
    private $modelObjects;
    
    /**
     * @var SoilModel $soilModel
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SoilModel", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="soil_model_id", referencedColumnName="id", onDelete="SET NULL")
     * @JMS\Type("AppBundle\Entity\SoilModel")
     * @JMS\Groups({"details", "modeldetails"})
     **/
    private $soilModel;

    /**
     * @var GridSize
     *
     * @ORM\Column(name="grid_size", type="grid_size", nullable=true)
     *
     * @JMS\Type("AppBundle\Model\Interpolation\GridSize")
     * @JMS\Groups({"details", "modeldetails"})
     */
    private $gridSize;

    /**
     * @var BoundingBox
     *
     * @ORM\Column(name="bounding_box", type="bounding_box", nullable=true)
     *
     * @JMS\Type("AppBundle\Model\Interpolation\BoundingBox")
     * @JMS\Groups({"details", "modeldetails"})
     */
    private $boundingBox;

    /**
     * @var Area
     *
     * @JMS\Type("AppBundle\Entity\Area")
     * @JMS\Groups({"details", "modeldetails"})
     **/
    private $area;

    /**
     * @var array
     *
     * @ORM\Column(name="active_cells", type="json_array", nullable=true)
     */
    private $activeCells;

    /**
     * @var ArrayCollection
     * @JMS\Groups({"details", "modeldetails"})
     **/
    private $boundaries;

    /**
     * @var ArrayCollection
     * @JMS\Groups({"details", "modeldetails"})
     */
    private $observationPoints;

    /**
     * @var array
     *
     * @ORM\Column(name="calculation_properties", type="json_array")
     * @JMS\Groups({"details", "modeldetails"})
     */
    private $calculationProperties = array(
        "stress_periods" => array(),
        "initial_values" => array(
            "property" => null,
            "head_from_top_elevation" => null,
            "steady_state_calculation" => true,
            "interpolation" => array()          // of observationPoints
        ),
    );

    /**
     * @var ArrayCollection
     */
    private $scenarios;

    /**
     * ModFlowModel constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->boundaries = new ArrayCollection();
        $this->modelObjects = new ArrayCollection();
        $this->observationPoints = new ArrayCollection();
        $this->gridSize = new GridSize(50, 50);
        $this->boundingBox = new BoundingBox();
    }

    /**
     * Add soilModelObject
     *
     * @param \AppBundle\Entity\ModelObject $modelObject
     * @return $this
     */
    public function addModelObject(ModelObject $modelObject)
    {
        if (!$this->modelObjects->contains($modelObject)){
            $this->modelObjects[] = $modelObject;
        }

        return $this;
    }

    /**
     * Remove soilModelObject
     *
     * @param \AppBundle\Entity\ModelObject $modelObject
     * @return $this
     */
    public function removeModelObject(ModelObject $modelObject)
    {
        if ($this->modelObjects->contains($modelObject)){
            $this->modelObjects->removeElement($modelObject);
        }
        return $this;
    }

    /**
     * Get modelObjects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getModelObjects()
    {
        return $this->modelObjects;
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
     * @return array
     */
    public function getActiveCells()
    {
        return $this->activeCells;
    }

    /**
     * @param array $activeCells
     * @return ModFlowModel
     */
    public function setActiveCells($activeCells)
    {
        $this->activeCells = $activeCells;
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
     * @return bool
     */
    public function hasSoilModel()
    {
        if ($this->soilModel instanceof SoilModel){
            return true;
        }

        return false;
    }

    /**
     * Get boundaries
     *
     * @return BoundaryModelObject|ArrayCollection
     */
    public function getBoundaries()
    {
        return $this->boundaries;
    }

    /**
     * @param BoundaryModelObject $boundary
     * @return $this
     */
    public function addBoundary(BoundaryModelObject $boundary)
    {
        if ($this->boundaries == null) {
            $this->boundaries = new ArrayCollection();
        }

        if (!$this->boundaries->contains($boundary)) {
            $this->boundaries->add($boundary);
        }

        return $this;
    }

    /**
     * Remove boundary
     *
     * @param BoundaryModelObject $boundary
     */
    public function removeBoundary(BoundaryModelObject $boundary)
    {
        if ($this->boundaries->contains($boundary)){
            $this->boundaries->removeElement($boundary);
        }
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
        if ($this->observationPoints == null) {
            $this->observationPoints = new ArrayCollection();
        }

        if (!$this->observationPoints->contains($observationPoint)) {
            $this->observationPoints[] = $observationPoint;
        }

        return $this;
    }

    /**
     * @param ObservationPoint $observationPoint
     * @return $this
     */
    public function removeObservationPoint(ObservationPoint $observationPoint)
    {
        if ($this->observationPoints->contains($observationPoint)){
            $this->observationPoints->removeElement($observationPoint);
        }

        return $this;
    }

    /**
     * Add stressPeriod
     *
     * @param $stressPeriod
     *
     * @return ModFlowModel
     */
    public function addStressPeriod($stressPeriod)
    {
        $this->calculationProperties["stress_periods"][] = $stressPeriod;

        return $this;
    }

    /**
     * Get stressPeriods
     *
     * @return array
     */
    public function getStressPeriods()
    {
        return $this->calculationProperties["stress_periods"];
    }

    /**
     * Set stressPeriods
     *
     * @param $stressPeriods
     * @return ModFlowModel
     */
    public function setStressPeriods($stressPeriods)
    {
        $this->calculationProperties["stress_periods"] = $stressPeriods;
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

    /**
     * Set initValues
     *
     * @param array $initValues
     *
     * @return ModFlowModel
     */
    public function setInitialValues($initValues)
    {
        $this->calculationProperties["initial_values"] = $initValues;

        return $this;
    }

    /**
     * Get initValues
     *
     * @return array
     */
    public function getInitialValues()
    {
        return $this->calculationProperties["initial_values"];
    }

    /**
     * @return GridSize
     */
    public function getGridSize()
    {
        return $this->gridSize;
    }

    /**
     * @param GridSize $gridSize
     * @return $this
     */
    public function setGridSize($gridSize)
    {
        $this->gridSize = $gridSize;
        return $this;
    }
    
    /**
     * @return BoundingBox
     */
    public function getBoundingBox()
    {
        return $this->boundingBox;
    }

    /**
     * @param BoundingBox $boundingBox
     * @return ModFlowModel
     */
    public function setBoundingBox($boundingBox)
    {
        $this->boundingBox = $boundingBox;
        return $this;
    }

    /**
     * @return string
     */
    public function createTextOverview()
    {
       return sprintf("%s Rows, %s Columns, %s Layers",
       $this->gridSize->getNY(),
       $this->gridSize->getNX(),
       $this->soilModel->getGeologicalLayers()->count());
    }

    /**
     * @param ModelScenario $scenario
     */
    public function registerScenario(ModelScenario $scenario) {
        $this->scenarios[] = $scenario;
    }

    /**
     * @return ArrayCollection
     */
    public function getScenarios(){
        return $this->scenarios;
    }

    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        if (!is_null($this->area)) {
            $this->addModelObject($this->area);
        }

        if ($this->boundaries->count() > 0 ) {
            foreach ($this->boundaries as $boundary) {
                $this->addModelObject($boundary);
            }
        }

        if (!$this->observationPoints) {
            $this->observationPoints = new ArrayCollection();
        }

        if ($this->observationPoints->count() > 0 ) {
            foreach ($this->observationPoints as $observationPoint) {
                $this->addModelObject($observationPoint);
            }
        }
    }
    
    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        foreach ($this->getModelObjects() as $modelObject) {
            if ($modelObject instanceof Area) {
                $this->area = $modelObject;
                $this->removeModelObject($modelObject);
            }

            if ($modelObject instanceof BoundaryModelObject) {
                $this->addBoundary($modelObject);
                $this->removeModelObject($modelObject);
            }

            if ($modelObject instanceof ObservationPoint) {
                $this->addObservationPoint($modelObject);
                $this->removeModelObject($modelObject);
            }
        }
    }
}
