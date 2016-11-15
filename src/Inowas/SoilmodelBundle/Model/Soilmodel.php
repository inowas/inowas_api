<?php

namespace Inowas\Soilmodel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Inowas\ModflowBundle\Model\ValueObject\BoundingBox;
use Inowas\ModflowBundle\Model\ValueObject\GridSize;
use Ramsey\Uuid\Uuid;

class Soilmodel
{
    /**
     * @var Uuid
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var GridSize
     */
    protected $gridSize;

    /**
     * @var BoundingBox
     */
    protected $boundingBox;

    /**
     * @var ArrayCollection
     */
    private $layers;

    /**
     * @var ArrayCollection
     */
    private $boreholes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->layers = new ArrayCollection();
        $this->boreholes = new ArrayCollection();
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
     * @return Soilmodel
     */
    public function setName(string $name): Soilmodel
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
     * @return Soilmodel
     */
    public function setDescription(string $description): Soilmodel
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
     * @return Soilmodel
     */
    public function setGridSize(GridSize $gridSize): Soilmodel
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
     * @return Soilmodel
     */
    public function setBoundingBox(BoundingBox $boundingBox): Soilmodel
    {
        $this->boundingBox = $boundingBox;
        return $this;
    }

    /**
     * @param Layer $layer
     * @return Soilmodel
     */
    public function addLayer(Layer $layer): Soilmodel
    {
        $this->layers[] = $layer->setOrder($this->layers->count());
        return $this;
    }

    /**
     * @param Layer $layer
     * @return Soilmodel
     */
    public function removeLayer(Layer $layer): Soilmodel
    {
        if ($this->layers->contains($layer)){
            $this->layers->removeElement($layer);
        }
        return $this;
    }

    /**
     * @return Collection
     */
    public function getLayers(): Collection
    {
        return $this->layers;
    }

    /**
     * @param BoreHole $boreHole
     * @return Soilmodel
     */
    public function addBoreHole(BoreHole $boreHole): Soilmodel
    {
        $this->boreholes[] = $boreHole;
        return $this;
    }

    /**
     * @param BoreHole $boreHole
     * @return Soilmodel
     */
    public function removeBoreHole(BoreHole $boreHole): Soilmodel
    {
        if ($this->boreholes->contains($boreHole)){
            $this->boreholes->removeElement($boreHole);
        }
        return $this;
    }

    /**
     * @return Collection
     */
    public function getBoreHoles(): Collection
    {
        return $this->boreholes;
    }
}
