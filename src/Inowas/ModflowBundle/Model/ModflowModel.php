<?php

namespace Inowas\ModflowBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Inowas\Common\Id\ModflowId;
use Inowas\ModflowBundle\Model\Boundary\Boundary;
use Inowas\ModflowBundle\Model\Boundary\StressPeriod;
use Inowas\SoilmodelBundle\Model\Soilmodel;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * Class ModflowModel
 * @package Inowas\ModflowBundle\Model
 */
class ModflowModel implements ModflowModelInterface
{
    /** @var Uuid */
    private $id;

    /** @var string */
    private $name = '';

    /** @var string */
    private $description = '';

    /** @var GridSize */
    private $gridSize;

    /** @var BoundingBox  */
    private $boundingBox;

    /** @var  Area */
    private $area;

    /** @var  Uuid */
    private $soilmodelId;

    /** @var ArrayCollection  */
    private $boundaries;

    /** @var  \DateTime */
    private $start;

    /** @var  \DateTime */
    private $end;

    /** @var TimeUnit */
    private $timeUnit;

    public static function createWithModflowId(ModflowId $id): ModflowModel
    {
        $self = new self();
        $self->id = Uuid::fromString($id->toString());
        return $self;
    }

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->gridSize = new GridSize(50, 50);
        $this->boundingBox = new BoundingBox();
        $this->area = AreaFactory::create();
        $this->boundaries = new ArrayCollection();
        $this->timeUnit = TimeUnit::fromString('day');
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
     * @return Area|null
     */
    public function getArea()
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
     * @return Uuid|null
     */
    public function getSoilmodelId()
    {
        return $this->soilmodelId;
    }

    /**
     * @param Uuid $soilModel
     * @return ModflowModel
     */
    public function setSoilmodelId(Uuid $soilModel): ModflowModel
    {
        $this->soilmodelId = $soilModel;
        return $this;
    }

    /**
     * @param string|null $type
     * @return Collection
     */
    public function getBoundaries(string $type=null): Collection
    {
        if (is_null($type)){
            return $this->boundaries;
        }

        $boundaries = new ArrayCollection();
        /** @var Boundary $boundary */
        foreach ($this->boundaries as $boundary){
            if ($boundary->getType() == $type){
                $boundaries->add($boundary);
            }
        }
        return $boundaries;
    }

    /**
     * @param Uuid $id
     * @return Boundary|null
     */
    public function getBoundary(Uuid $id)
    {
        /** @var Boundary $boundary */
        foreach ($this->getBoundaries() as $boundary){
            if ($boundary->getId() == $id){
                return $boundary;
            }
        }

        return null;
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
     * @param Boundary $boundary
     * @return ModflowModel
     */
    public function removeBoundary(Boundary $boundary): ModflowModel
    {
        if ($this->boundaries->contains($boundary)){
            $this->boundaries->removeElement($boundary);
        }
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
        return ($this->soilmodelId instanceof Soilmodel);
    }

    /**
     * @return \DateTime
     */
    public function getStart(): \DateTime
    {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     * @return ModflowModel
     */
    public function setStart(\DateTime $start): ModflowModel
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnd(): \DateTime
    {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     * @return ModflowModel
     */
    public function setEnd(\DateTime $end): ModflowModel
    {
        $this->end = $end;
        return $this;
    }

    /**
     * @return TimeUnit
     */
    public function getTimeUnit(): TimeUnit
    {
        return $this->timeUnit;
    }

    /**
     * @param TimeUnit $timeUnit
     * @return ModflowModel
     */
    public function setTimeUnit(TimeUnit $timeUnit): ModflowModel
    {
        $this->timeUnit = $timeUnit;
        return $this;
    }

    /**
     * @return GlobalStressPeriods
     */
    public function getGlobalStressPeriods(){

        $globalStressPeriods = new GlobalStressPeriods($this->start, $this->end, $this->timeUnit);

        /** @var Boundary $boundary */
        foreach ($this->getBoundaries() as $boundary){
            /** @var StressPeriod $stressPeriod */
            foreach ($boundary->getStressPeriods() as $stressPeriod){
                $globalStressPeriods->addStressPeriod($stressPeriod);
            }
        }

        return $globalStressPeriods;
    }
}
