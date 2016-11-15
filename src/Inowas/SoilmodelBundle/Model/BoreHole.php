<?php

namespace Inowas\Soilmodel\Model;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;


class BoreHole extends SoilmodelObject
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

    /** @var  Point */
    private $point;

    /** @var ArrayCollection  */
    private $layers;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->layers = new ArrayCollection();
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
     * @return BoreHole
     */
    public function setName(string $name = null): BoreHole
    {
        if (is_null($name)){
            $name = '';
        }

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
     * @return BoreHole
     */
    public function setDescription(string $description): BoreHole
    {
        if (is_null($description)){
            $description = '';
        }

        $this->description = $description;
        return $this;
    }

    /**
     * @param Point $point
     * @return $this
     */
    public function setPoint(Point $point)
    {
        $this->point = $point;

        return $this;
    }

    /**
     * @return Point
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * @param Layer $layer
     * @return $this
     */
    public function addLayer(Layer $layer){
        $this->layers[] = $layer->setOrder($this->layers->count());
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getLayers(){
        return $this->layers;
    }
}
