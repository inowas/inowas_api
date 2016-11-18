<?php

namespace AppBundle\Entity;

use AppBundle\Exception\InvalidArgumentException;
use AppBundle\Model\ActiveCells;
use AppBundle\Model\BoundaryInterface;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\StressPeriodFactory;
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
     * @JMS\Groups({"details", "list", "modeldetails", "modelProperties"})
     */
    private $gridSize;

    /**
     * @var BoundingBox
     *
     * @ORM\Column(name="bounding_box", type="bounding_box", nullable=true)
     *
     * @JMS\Type("AppBundle\Model\BoundingBox")
     * @JMS\Groups({"details", "list", "modeldetails", "modelProperties"})
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
     * @var ArrayCollection
     */
    private $stressPeriods;

    /**
     * @var FlopyCalculationProperties
     *
     * @ORM\Column(name="calculation_properties", type="flopy_calculation_properties", nullable=true)
     */
    private $calculationProperties;

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
     * @JMS\VirtualProperty
     * @JMS\SerializedName("active_cells")
     * @JMS\Groups({"details", "modeldetails", "modelProperties"})
     * @return ActiveCells
     */
    public function getActiveCells()
    {
        if ($this->area instanceof Area){
            return $this->area->getActiveCells();
        }

        return null;
    }

    /**
     * @param ActiveCells $activeCells
     * @return $this
     */
    public function setActiveCells(ActiveCells $activeCells)
    {
        if (! $this->area instanceof Area){
            throw new InvalidArgumentException('The model needs an area to update activeCells.');
        }

        $this->area->setActiveCells($activeCells);
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
     * @return ArrayCollection
     */
    public function getStressPeriods()
    {
        if (! $this->stressPeriods instanceof ArrayCollection){
            $this->stressPeriods = $this->loadStressPeriodsFromBoundaries();
            $this->stressPeriods = $this->sortStressPeriods($this->stressPeriods);

            if (!is_null($this->getCalculationProperties()) && $this->getCalculationProperties()->getInitialValues() == FlopyCalculationProperties::INITIAL_VALUE_STEADY_STATE_CALCULATION){
                if ($this->stressPeriods->count() > 0) {
                    $this->stressPeriods->first()->setSteady(true);
                }
            }
        }

        return $this->stressPeriods;
    }

    /**
     * @return ArrayCollection
     */
    protected function loadStressPeriodsFromBoundaries()
    {
        $bSps = new ArrayCollection();

        /** @var BoundaryInterface $boundary */
        foreach ($this->boundaries as $boundary) {

            if ($boundary->getStressPeriods() === null || $boundary->getStressPeriods()->count() == 0) {
                continue;
            }

            $bSps = new ArrayCollection(
                array_merge($bSps->toArray(), $boundary->getStressPeriods()->toArray())
            );
        }

        return $bSps;
    }

    /**
     * @param ArrayCollection $bSps
     * @return ArrayCollection
     */
    protected function sortStressPeriods(ArrayCollection $bSps)
    {
        $startDates = array();
        $endDates = array();
        foreach ($bSps as $sp){
            $startDates[] = $sp->getDateTimeBegin();
            $endDates[] = $sp->getDateTimeEnd();
        }

        $startDates = array_map("unserialize", array_unique(array_map("serialize", $startDates)));
        usort($startDates, function($a, $b) {return ($a < $b) ? -1 : 1;});
        usort($endDates, function($a, $b) {return ($a < $b) ? -1 : 1;});
        $endDate = end($endDates);

        $sps = new ArrayCollection();

        $ni = count($startDates);
        for($i=0; $i<$ni; $i++) {

            $dateTimeStart = $startDates[$i];
            if ($i != count($startDates)-1) {
                /** @var \DateTime $dateTimeEnd */
                $dateTimeEnd = clone $startDates[$i+1];
                $dateTimeEnd->modify('-1day');
            } else{
                $dateTimeEnd = $endDate;
            }

            foreach ($bSps as $bSp) {
                if ($bSp->getDateTimeBegin() == $dateTimeStart){
                    $stressPeriodWithStartDate = $bSp;
                    break;
                }
            }

            $steady = false;
            if (isset($stressPeriodWithStartDate)){
                $steady = $stressPeriodWithStartDate->isSteady();
            }

            $sps->add(StressPeriodFactory::create()
                ->setDateTimeBegin($dateTimeStart)
                ->setDateTimeEnd($dateTimeEnd)
                ->setSteady($steady)
            );
        }

        return $sps;
    }

    /**
     * Set calculationProperties
     *
     * @param FlopyCalculationProperties $calculationProperties
     *
     * @return ModFlowModel
     */
    public function setCalculationProperties($calculationProperties)
    {
        $this->calculationProperties = $calculationProperties;
        return $this;
    }

    /**
     * @return FlopyCalculationProperties
     */
    public function getCalculationProperties()
    {
        return $this->calculationProperties;
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
     * @param ModflowModelScenario $scenario
     */
    public function registerScenario(ModflowModelScenario $scenario) {
        if (! $this->scenarios->contains($scenario)){
            $this->scenarios[] = $scenario;
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getScenarios(){
        return $this->scenarios;
    }

    /**
     * @param BoundaryModelObject $origin
     * @param BoundaryModelObject $newBoundary
     * @return mixed
     */
    public function changeBoundary(BoundaryModelObject $origin, BoundaryModelObject $newBoundary)
    {
        if ($this->boundaries instanceof ArrayCollection){

            /** @var BoundaryModelObject $boundary */
            foreach ($this->boundaries as $boundary){
                if ($boundary->getId() == $origin->getId()){
                    $this->boundaries->removeElement($boundary);
                    $this->boundaries->add($newBoundary);
                }
            }
        }

        return $this;
    }

    /**
     * @param FlopyCalculationProperties $calculationProperties
     * @return mixed
     */
    public function addCalculationProperties($calculationProperties)
    {
        $this->calculationProperties = $calculationProperties;
        return $this;
    }

    /**
     * @return bool
     */
    public function isScenario()
    {
        return false;
    }

    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        $this->modelObjects = new ArrayCollection();

        if (!is_null($this->area)) {
            $this->addModelObject($this->area);
        }

        if ($this->boundaries->count() > 0 ) {
            foreach ($this->boundaries as $boundary) {
                $this->addModelObject($boundary);
            }
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
        $this->boundaries = new ArrayCollection();
        $this->observationPoints = new ArrayCollection();

        foreach ($this->getModelObjects() as $modelObject) {

            if ($modelObject instanceof Area) {
                $this->area = $modelObject;
            }

            if ($modelObject instanceof BoundaryModelObject) {
                $this->boundaries->add($modelObject);
            }

            if ($modelObject instanceof ObservationPoint) {
                $this->observationPoints->add($modelObject);
            }
        }
    }
}
