<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

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
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"details", "modeldetails"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255, nullable=true)
     * @JMS\Groups({"details", "modeldetails"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @JMS\Groups({"details"})
     */
    private $description;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownedSoilModels")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     * @JMS\MaxDepth(1)
     */
    private $owner;

    /**
     * @var ArrayCollection ModelObject $modelObjects
     *
     * @ORM\ManyToMany(targetEntity="ModelObject", mappedBy="soilModels", cascade={"persist", "remove"})
     **/
    private $modelObjects;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean")
     * @JMS\Groups({"details", "modeldetails"})
     */
    private $public;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     * @JMS\Groups({"details"})
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModified", type="datetime")
     * @JMS\Groups({"details"})
     */
    private $dateModified;

    /**
     * @var ArrayCollection
     * @JMS\Type("ArrayCollection<AppBundle\Entity\GeologicalLayer>")
     * @JMS\Groups({"details", "modeldetails"})
     */
    private $geologicalLayers;

    /**
     * @var ArrayCollection
     */
    private $geologicalPoints;

    /**
     * @var ArrayCollection
     */
    private $geologicalUnits;

    /**
     * @var Area
     */
    private $area;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * @return SoilModel
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
     * @return SoilModel
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
     * @return SoilModel
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
     * @return SoilModel
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
     * @return SoilModel
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
     * @param \AppBundle\Entity\User $owner
     *
     * @return SoilModel
     */
    public function setOwner(\AppBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \AppBundle\Entity\User
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
     * @return SoilModel
     */
    public function addModelObject(ModelObject $modelObject)
    {
        if (!$modelObject->getSoilModels()->contains($this))
        {
            $modelObject->addSoilModel($this);
        }
        $this->modelObjects[] = $modelObject;

        return $this;
    }

    /**
     * Remove soilModelObject
     *
     * @param \AppBundle\Entity\ModelObject $modelObject
     */
    public function removeModelObject(ModelObject $modelObject)
    {
        if ($modelObject->getSoilModels()->contains($modelObject))
        {
            $this->modelObjects->removeElement($modelObject);
        }
        $modelObject->removeSoilModel($this);
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
        return $this->geologicalLayers;
    }

    /**
     * @param GeologicalLayer $geologicalLayer
     * @return $this
     */
    public function addGeologicalLayer(GeologicalLayer $geologicalLayer)
    {
        if (is_null($this->geologicalLayers))
        {
            $this->geologicalLayers = new ArrayCollection();
        }

        if (!$this->geologicalLayers->contains($geologicalLayer))
        {
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
        if (is_null($this->geologicalPoints))
        {
            $this->geologicalPoints = new ArrayCollection();
        }

        if (!$this->geologicalPoints->contains($geologicalPoint))
        {
            $this->geologicalPoints[] = $geologicalPoint;
        }

        return $this;
    }

    /**
     * @param GeologicalPoint $geologicalPoint
     */
    public function removeGeologicalPoint(GeologicalPoint $geologicalPoint)
    {
        if ($this->geologicalPoints->contains($geologicalPoint))
        {
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
        if (is_null($this->geologicalUnits))
        {
            $this->geologicalUnits = new ArrayCollection();
        }

        if (!$this->geologicalUnits->contains($geologicalUnit))
        {
            $this->geologicalUnits[] = $geologicalUnit;
        }

        return $this;
    }

    /**
     * @param GeologicalUnit $geologicalUnit
     */
    public function removeGeologicalUnit(GeologicalUnit $geologicalUnit)
    {
        if ($this->geologicalUnits->contains($geologicalUnit))
        {
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
        if ($this->geologicalPoints->count() > 0 )
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

        if ($this->geologicalLayers->count() > 0 )
        {
            foreach ($this->geologicalLayers as $geologicalLayer)
            {
                $this->addModelObject($geologicalLayer);
            }
        }

        if ($this->geologicalUnits->count() > 0 )
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
