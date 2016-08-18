<?php

namespace AppBundle\Entity;

use AppBundle\Model\ActiveCells;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
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
     * @JMS\Type("AppBundle\Model\GridSize")
     * @JMS\Groups({"details", "modeldetails", "modelProperties"})
     */
    private $gridSize;

    /**
     * @var BoundingBox
     *
     * @ORM\Column(name="bounding_box", type="bounding_box", nullable=true)
     *
     * @JMS\Type("AppBundle\Model\BoundingBox")
     * @JMS\Groups({"details", "modeldetails", "modelProperties"})
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
     * @var ActiveCells
     *
     * @ORM\Column(name="active_cells", type="active_cells", nullable=true)
     * @JMS\Groups({"modelProperties"})
     */
    private $activeCells;

    /**
     * @var ArrayCollection
     *
     * @JMS\Groups({"details", "modeldetails"})
     **/
    private $boundaries;

    /**
     * @var ArrayCollection
     * 
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
            "interpolation" => array()
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
        $this->observationPoints = new ArrayCollection();
        $this->gridSize = new GridSize(50, 50);
        $this->boundingBox = new BoundingBox();
        $this->scenarios = new ArrayCollection();
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
    public function setArea(Area $area=null)
    {
        $this->area = $area;
        return $this;
    }

    /**
     * @return ActiveCells
     */
    public function getActiveCells()
    {
        return $this->activeCells;
    }

    /**
     * @param ActiveCells $activeCells
     * @return $this
     */
    public function setActiveCells(ActiveCells $activeCells)
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
        if (!$this->getBoundaries()->contains($boundary)) {
            $this->getBoundaries()->add($boundary);
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
     * @return int
     */
    public function getNumberOfStressPeriods(){
        return count($this->calculationProperties["stress_periods"]);
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
        $nx = 0;
        $ny = 0;
        $layersCount = 0;

        if ($this->gridSize instanceof GridSize){
            $nx = $this->gridSize->getNX();
            $ny = $this->gridSize->getNY();
        }

        if ($this->hasSoilModel() && $this->soilModel->hasGeologicalLayers()){
            $layersCount = $this->soilModel->getGeologicalLayers()->count();
        }

       return sprintf("%s Rows, %s Columns, %s Layers", $ny, $nx, $layersCount);
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
                $this->removeBoundary($boundary);
            }
        }

        if ($this->observationPoints->count() > 0 ) {
            foreach ($this->observationPoints as $observationPoint) {
                $this->addModelObject($observationPoint);
                $this->removeObservationPoint($observationPoint);
            }
        }
    }
    
    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        $this->boundaries = new ArrayCollection();
        $this->observationPoints = new ArrayCollection();

        foreach ($this->getModelObjects() as $modelObject) {

            if ($modelObject instanceof Area) {
                $this->area = $modelObject;
                $this->removeModelObject($modelObject);
            }

            if ($modelObject instanceof BoundaryModelObject) {
                $this->boundaries->add($modelObject);
                $this->removeModelObject($modelObject);
            }

            if ($modelObject instanceof ObservationPoint) {
                $this->observationPoints->add($modelObject);
                $this->removeModelObject($modelObject);
            }
        }
    }
}
