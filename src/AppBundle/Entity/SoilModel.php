<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * SoilModel
 *
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
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255)
     * @JMS\Groups({"projectList", "projectDetails"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @JMS\Groups({"projectList", "projectDetails"})
     */
    private $description;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownedSoilModels")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     * @JMS\MaxDepth(3)
     * @JMS\Groups({"projectDetails"})
     */
    private $owner;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\SoilModelObject")
     * @ORM\JoinTable(name="soil_models_modelobjects",
     *      joinColumns={@ORM\JoinColumn(name="soil_model_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="modelobject_id", referencedColumnName="id")}
     *      )
     */
    private $soilModelObjects;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean")
     * @JMS\Groups({"projectList", "projectDetails"})
     */
    private $public;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     * @JMS\Groups({"projectDetails"})
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModified", type="datetime")
     * @JMS\Groups({"projectDetails"})
     */
    private $dateModified;

    /**
     * @var ArrayCollection
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
     * Constructor
     */
    public function __construct()
    {
        $this->soilModelObjects = new ArrayCollection();
        $this->geologicalLayers = new ArrayCollection();
        $this->geologicalPoints = new ArrayCollection();
        $this->geologicalUnits = new ArrayCollection();
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
    public function getSoilModelObjects()
    {
        return $this->soilModelObjects;
    }

    /**
     * Add soilModelObject
     *
     * @param \AppBundle\Entity\SoilModelObject $soilModelObject
     *
     * @return SoilModel
     */
    public function addSoilModelObject(\AppBundle\Entity\SoilModelObject $soilModelObject)
    {
        $this->soilModelObjects[] = $soilModelObject;

        return $this;
    }

    /**
     * Remove soilModelObject
     *
     * @param \AppBundle\Entity\SoilModelObject $soilModelObject
     */
    public function removeSoilModelObject(\AppBundle\Entity\SoilModelObject $soilModelObject)
    {
        $this->soilModelObjects->removeElement($soilModelObject);
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
        $this->geologicalLayers[] = $geologicalLayer;

        return $this;
    }

    /**
     * @param GeologicalLayer $geologicalLayer
     */
    public function removeGeologicalLayer(GeologicalLayer $geologicalLayer)
    {
        $this->geologicalLayers->removeElement($geologicalLayer);
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
        $this->geologicalPoints[] = $geologicalPoint;

        return $this;
    }

    /**
     * @param GeologicalPoint $geologicalPoint
     */
    public function removeGeologicalPoint(GeologicalPoint $geologicalPoint)
    {
        $this->geologicalPoints->removeElement($geologicalPoint);
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
        $this->geologicalUnits[] = $geologicalUnit;

        return $this;
    }

    /**
     * @param GeologicalUnit $geologicalUnit
     */
    public function removeGeologicalUnit(GeologicalUnit $geologicalUnit)
    {
        $this->geologicalUnits->removeElement($geologicalUnit);
    }

    /**
     * on PrePersist event all
     * +layers
     * +units
     * +points
     * have to be written to the soilModelObjects-Array
     *
     * @ORM\PrePersist()
     */
    public function prePersist()
    {

        if ($this->geologicalPoints->count() > 0 )
        {
            foreach ($this->geologicalPoints as $geologicalPoint)
            {
                $this->addSoilModelObject($geologicalPoint);
            }
        }

        if ($this->geologicalLayers->count() > 0 )
        {
            foreach ($this->geologicalLayers as $geologicalLayer)
            {
                $this->addSoilModelObject($geologicalLayer);
            }
        }

        if ($this->geologicalUnits->count() > 0 )
        {
            foreach ($this->geologicalUnits as $geologicalUnit)
            {
                $this->addSoilModelObject($geologicalUnit);
            }
        }
    }

    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        foreach ($this->getSoilModelObjects() as $soilModelObject)
        {
            if ($soilModelObject instanceof GeologicalLayer)
            {
                $this->addGeologicalLayer($soilModelObject);
                $this->removeSoilModelObject($soilModelObject);
            }

            if ($soilModelObject instanceof GeologicalPoint)
            {
                $this->addGeologicalPoint($soilModelObject);
                $this->removeSoilModelObject($soilModelObject);
            }

            if ($soilModelObject instanceof GeologicalUnit)
            {
                $this->addGeologicalUnit($soilModelObject);
                $this->removeSoilModelObject($soilModelObject);
            }
        }
    }
}
