<?php

namespace Inowas\ModflowBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Inowas\ModflowBundle\Model\Boundary\Boundary;
use Inowas\Soilmodel\Model\Soilmodel;
use Ramsey\Uuid\Uuid;

class ModflowModel
{
    /** @var Uuid */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var GridSize */
    private $gridSize;

    /** @var BoundingBox  */
    private $boundingBox;

    /** @var  Area */
    private $area;

    /** @var  Soilmodel */
    private $soilmodel;

    /** @var ArrayCollection  */
    private $boundaries;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->gridSize = new GridSize(50, 50);
        $this->boundingBox = new BoundingBox();
        $this->boundaries = new ArrayCollection();
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ModflowModel
     */
    public function setName(string $name): ModflowModel
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return ModflowModel
     */
    public function setDescription(string $description): ModflowModel
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return GridSize
     */
    public function getGridSize(): GridSize
    {
        return $this->gridSize;
    }

    /**
     * @param GridSize $gridSize
     * @return ModflowModel
     */
    public function setGridSize(GridSize $gridSize): ModflowModel
    {
        $this->gridSize = $gridSize;
        return $this;
    }

    /**
     * @return BoundingBox
     */
    public function getBoundingBox(): BoundingBox
    {
        return $this->boundingBox;
    }

    /**
     * @param BoundingBox $boundingBox
     * @return ModflowModel
     */
    public function setBoundingBox(BoundingBox $boundingBox): ModflowModel
    {
        $this->boundingBox = $boundingBox;
        return $this;
    }

    /**
     * @return Area
     */
    public function getArea(): Area
    {
        return $this->area;
    }

    /**
     * @param Area $area
     * @return ModflowModel
     */
    public function setArea(Area $area): ModflowModel
    {
        $this->area = $area;
        return $this;
    }

    /**
     * @return Soilmodel
     */
    public function getSoilmodel(): Soilmodel
    {
        return $this->soilmodel;
    }

    /**
     * @param Soilmodel $soilModel
     * @return ModflowModel
     */
    public function setSoilmodel(Soilmodel $soilModel): ModflowModel
    {
        $this->soilmodel = $soilModel;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getBoundaries(): ArrayCollection
    {
        return $this->boundaries;
    }

    /**
     * @param Boundary $boundary
     * @return ModflowModel
     */
    public function addBoundary(Boundary $boundary): ModflowModel
    {
        $this->boundaries->add($boundary);
        return $this;
    }

    /**
     * @param ArrayCollection $boundaries
     * @return ModflowModel
     */
    public function setBoundaries(ArrayCollection $boundaries): ModflowModel
    {
        $this->boundaries = $boundaries;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasSoilModel(): bool
    {
        return ($this->soilmodel instanceof Soilmodel);
    }

    /**
     * @return ArrayCollection
     */
    public function getGlobalStressPeriods(){
        $globalStressPeriods = new ArrayCollection();

        /** @var Boundary $boundary */
        foreach ($this->boundaries as $boundary){
            $globalStressPeriods = new ArrayCollection(
                array_merge($globalStressPeriods->toArray(), $boundary->getStressPeriods()->toArray())
            );
        }

        $startDates = array();
        $endDates = array();
        foreach ($globalStressPeriods as $stressPeriod){
            $startDates[] = $stressPeriod->getDateTimeBegin();
            $endDates[] = $stressPeriod->getDateTimeEnd();
        }

        $startDates = array_map("unserialize", array_unique(array_map("serialize", $startDates)));
        usort($startDates, function($a, $b) {return ($a < $b) ? -1 : 1;});
        usort($endDates, function($a, $b) {return ($a < $b) ? -1 : 1;});
        $endDate = end($endDates);

        $stressPeriods = new ArrayCollection();
        for($i=0; $i<count($startDates); $i++) {

            $dateTimeStart = $startDates[$i];
            if ($i != count($startDates)-1) {
                /** @var \DateTime $dateTimeEnd */
                $dateTimeEnd = clone $startDates[$i+1];
                $dateTimeEnd->modify('-1day');
            } else{
                $dateTimeEnd = $endDate;
            }

            foreach ($globalStressPeriods as $globalStressPeriod) {
                if ($globalStressPeriod->getDateTimeBegin() == $dateTimeStart){
                    $stressPeriodWithStartDate = $globalStressPeriod;
                    break;
                }
            }

            $steady = false;
            if (isset($stressPeriodWithStartDate)){
                $steady = $stressPeriodWithStartDate->isSteady();
            }

            $stressPeriods->add(StressPeriodFactory::create()
                ->setDateTimeBegin($dateTimeStart)
                ->setDateTimeEnd($dateTimeEnd)
                ->setSteady($steady)
            );
        }

        return $stressPeriods;
    }
}
