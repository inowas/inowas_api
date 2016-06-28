<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * SoilModel
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="soil_model")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SoilModelRepository")
 */
class SoilModel
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true)
     * @JMS\Type("string")
     * @JMS\Groups({"details", "modeldetails", "modelobjectdetails", "soilmodellist", "soilmodeldetails"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255, nullable=true)
     * @JMS\Groups({"details", "soilmodellist", "soilmodeldetails"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @JMS\Groups({"details", "soilmodellist", "soilmodeldetails"})
     */
    private $description;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     * @JMS\MaxDepth(1)
     * @JMS\Groups({"details", "soilmodellist", "soilmodeldetails"})
     */
    private $owner;

    /**
     * @var ArrayCollection ModelObject $modelObjects
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\ModelObject", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="soil_models_model_objects",
     *     joinColumns={@ORM\JoinColumn(name="soil_model_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="model_object_id", referencedColumnName="id", onDelete="CASCADE")}
     *     )
     **/
    private $modelObjects;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean")
     * @JMS\Groups({"details", "soilmodellist", "soilmodeldetails"})
     */
    private $public;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     * @JMS\Groups({"details", "soilmodellist",  "soilmodeldetails"})
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModified", type="datetime")
     * @JMS\Groups({"details", "soilmodellist",  "soilmodeldetails"})
     */
    private $dateModified;

    /**
     * @var ArrayCollection
     * @JMS\Type("ArrayCollection<AppBundle\Entity\GeologicalLayer>")
     * @JMS\Groups({"details", "modeldetails", "soilmodeldetails"})
     */
    private $geologicalLayers;

    /**
     * @var ArrayCollection
     * @JMS\Groups({"soilmodeldetails"})
     */
    private $geologicalPoints;

    /**
     * @var ArrayCollection
     */
    private $geologicalUnits;

    /**
     * @var Area
     * @JMS\Type("AppBundle\Entity\Area")
     * @JMS\Groups({"modeldetails", "soilmodeldetails"})
     */
    private $area;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->modelObjects = new ArrayCollection();
        $this->geologicalLayers = new ArrayCollection();
        $this->geologicalPoints = new ArrayCollection();
        $this->geologicalUnits = new ArrayCollection();

        $this->public = true;
        $this->dateCreated = new \DateTime();
        $this->dateModified = new \DateTime();
    }

    /**
     * Set id
     *
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set public
     *
     * @param boolean $public
     *
     * @return $this
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return boolean
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return $this
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     *
     * @return $this
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Set owner
     *
     * @param User $owner
     *
     * @return $this
     */
    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Get soilModelObjects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getModelObjects()
    {
        return $this->modelObjects;
    }

    /**
     * Add soilModelObject
     *
     * @param \AppBundle\Entity\ModelObject $modelObject
     *
     * @return $this
     */
    public function addModelObject(ModelObject $modelObject)
    {
        if (!$this->modelObjects->contains($modelObject)) {
            $this->modelObjects[] = $modelObject;
        }

        return $this;
    }

    /**
     * Remove soilModelObject
     *
     * @param \AppBundle\Entity\ModelObject $modelObject
     */
    public function removeModelObject(ModelObject $modelObject)
    {
        if ($this->modelObjects->contains($modelObject)) {
            $this->modelObjects->removeElement($modelObject);
        }
    }

    /**
     * @param Area $area
     * @return $this
     */
    public function setArea(Area $area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @return Area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @return ArrayCollection
     */
    public function getGeologicalLayers()
    {
        if (null == $this->geologicalLayers){
            $this->geologicalLayers = new ArrayCollection();
        }
        return $this->geologicalLayers;
    }

    /**
     * @return ArrayCollection
     */
    public function getSortedGeologicalLayers()
    {
        if (null != $this->geologicalLayers){

            $criteria = Criteria::create()->orderBy(array("order" => Criteria::ASC));
            $layers = $this->geologicalLayers->matching($criteria);
            return $layers;
        }

        return $this->geologicalLayers;
    }

    /**
     * @param GeologicalLayer $geologicalLayer
     * @return $this
     */
    public function addGeologicalLayer(GeologicalLayer $geologicalLayer)
    {
        if (is_null($this->geologicalLayers)) {
            $this->geologicalLayers = new ArrayCollection();
        }

        if (!$this->geologicalLayers->contains($geologicalLayer)) {
            if (null === $geologicalLayer->getOrder()) {
                $geologicalLayer->setOrder($this->geologicalLayers->count());
            }
            $this->geologicalLayers[] = $geologicalLayer;
        }

        return $this;
    }

    /**
     * @param GeologicalLayer $geologicalLayer
     */
    public function removeGeologicalLayer(GeologicalLayer $geologicalLayer)
    {
        if ($this->geologicalLayers->contains($geologicalLayer))
        {
            $this->geologicalLayers->removeElement($geologicalLayer);
        }
    }

    /**
     * @return bool
     */
    public function hasGeologicalLayers(){
        if (null == $this->getGeologicalLayers() || $this->getGeologicalLayers()->count() == 0) {
            return false;
        }

        return true;
    }

    /**
     * @param $layerNumber
     * @return null
     */
    public function getLayerByNumber($layerNumber){
        if ($this->hasGeologicalLayers()){

            /** @var GeologicalLayer $geologicalLayer */
            foreach ($this->getGeologicalLayers() as $geologicalLayer){
                if ($geologicalLayer->getOrder() == $layerNumber){
                    return $geologicalLayer;
                }
            }
        }

        return null;
    }

    /**
     * @return ArrayCollection
     */
    public function getGeologicalPoints()
    {
        return $this->geologicalPoints;
    }

    /**
     * @param GeologicalPoint $geologicalPoint
     * @return $this
     */
    public function addGeologicalPoint(GeologicalPoint $geologicalPoint)
    {
        if (is_null($this->geologicalPoints)) {
            $this->geologicalPoints = new ArrayCollection();
        }

        if (!$this->geologicalPoints->contains($geologicalPoint)) {
            $this->geologicalPoints[] = $geologicalPoint;
        }

        return $this;
    }

    /**
     * @param GeologicalPoint $geologicalPoint
     */
    public function removeGeologicalPoint(GeologicalPoint $geologicalPoint)
    {
        if ($this->geologicalPoints->contains($geologicalPoint)) {
            $this->geologicalPoints->removeElement($geologicalPoint);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getGeologicalUnits()
    {
        return $this->geologicalUnits;
    }

    /**
     * @param GeologicalUnit $geologicalUnit
     * @return $this
     */
    public function addGeologicalUnit(GeologicalUnit $geologicalUnit)
    {
        if (is_null($this->geologicalUnits)) {
            $this->geologicalUnits = new ArrayCollection();
        }

        if (!$this->geologicalUnits->contains($geologicalUnit)) {
            $this->geologicalUnits[] = $geologicalUnit;
        }

        return $this;
    }

    /**
     * @param GeologicalUnit $geologicalUnit
     */
    public function removeGeologicalUnit(GeologicalUnit $geologicalUnit)
    {
        if ($this->geologicalUnits->contains($geologicalUnit)) {
            $this->geologicalUnits->removeElement($geologicalUnit);
        }
    }

    /**
     * on PrePersist event all
     * +layers
     * +units
     * +points
     * have to be written to the soilModelObjects-Array
     *
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        if ($this->geologicalPoints && $this->geologicalPoints->count() > 0 )
        {
            /** @var GeologicalPoint $geologicalPoint */
            foreach ($this->geologicalPoints as $geologicalPoint)
            {
                $this->addModelObject($geologicalPoint);

                foreach ($geologicalPoint->getGeologicalUnits() as $geologicalUnit)
                {
                    $this->addGeologicalUnit($geologicalUnit);
                }
            }
        }

        if ($this->geologicalLayers && $this->geologicalLayers->count() > 0 )
        {
            foreach ($this->geologicalLayers as $geologicalLayer)
            {
                $this->addModelObject($geologicalLayer);
            }
        }

        if ($this->geologicalUnits && $this->geologicalUnits->count() > 0 )
        {
            foreach ($this->geologicalUnits as $geologicalUnit)
            {
                $this->addModelObject($geologicalUnit);
            }
        }

        if (!is_null($this->area))
        {
            $this->addModelObject($this->area);
        }
    }

    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        foreach ($this->getModelObjects() as $soilModelObject)
        {
            if ($soilModelObject instanceof GeologicalLayer)
            {
                $this->addGeologicalLayer($soilModelObject);
                $this->removeModelObject($soilModelObject);
            }

            if ($soilModelObject instanceof GeologicalPoint)
            {
                $this->addGeologicalPoint($soilModelObject);
                $this->removeModelObject($soilModelObject);
            }

            if ($soilModelObject instanceof GeologicalUnit)
            {
                $this->addGeologicalUnit($soilModelObject);
                $this->removeModelObject($soilModelObject);
            }

            if ($soilModelObject instanceof Area)
            {
                $this->setArea($soilModelObject);
                $this->removeModelObject($soilModelObject);
            }
        }
    }
}
