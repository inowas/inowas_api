<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * ModelObject
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModelObjectRepository")
 * @ORM\Table(name="model_objects")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({  "ar" = "Area",
 *                          "bo" = "Boundary",
 *                          "op" = "ObservationPoint",
 *                          "gp" = "GeologicalPoint",
 *                          "gl" = "GeologicalLayer",
 *                          "gu" = "GeologicalUnit",
 *                          "st" = "Stream"
 * })
 */
abstract class ModelObject
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true)
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "layerdetails", "modeldetails", "modelobjectdetails", "modelobjectlist", "soilmodeldetails"})
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     * @JMS\Groups({"modelobjectdetails", "modelobjectlist"})
     */
    protected $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @JMS\Groups({"list", "details", "layerdetails", "modelobjectdetails", "modelobjectlist", "soilmodeldetails"})
     */
    protected $name;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    protected $type = 'modelobject';

    /**
     * @var ArrayCollection SoilModel
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\SoilModel", inversedBy="modelObjects", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="soil_model_model_objects")
     * @JMS\Groups({"modelobjectdetails"})
     * @JMS\MaxDepth(1)
     **/
    protected $soilModels;

    /**
     * @var ArrayCollection Property
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Property", mappedBy="modelObject", cascade={"persist", "remove"})
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Property>")
     * @JMS\Groups({"details", "layerdetails", "modelobjectdetails", "soilmodeldetails"})
     */
    protected $properties;

    /**
     * @var ArrayCollection
     *
     * @JMS\Accessor(getter="getPropertyIds")
     * @JMS\Type("array<integer>")
     */
    protected $propertyIds;

    /**
     * @var ArrayCollection ObservationPoint
     *
     * @ORM\ManyToMany(targetEntity="ObservationPoint", inversedBy="modelObjects")
     * @ORM\JoinTable(name="model_objects_observation_points")
     * @JMS\Groups({"modelobjectdetails", "soilmodeldetails"})
     * @JMS\MaxDepth(5)
     */
    protected $observationPoints;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean")
     * @JMS\Groups({"list", "details", "layerdetails", "modelobjectdetails", "modelobjectlist", "soilmodeldetails"})
     */
    protected $public;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime")
     * @JMS\Groups({"modelobjectdetails", "soilmodeldetails"})
     */
    protected $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modified", type="datetime")
     * @JMS\Groups({"modelobjectdetails", "soilmodeldetails"})
     */
    protected $dateModified;

    /**
     * Constructor
     * @param User $owner
     * @param $public
     */
    public function __construct(User $owner = null, $public = false)
    {
        $this->id = Uuid::uuid4();
        $this->owner = $owner;
        $this->public = $public;
        $this->soilModels = new ArrayCollection();
        $this->propertyIds = new ArrayCollection();
        $this->properties = new ArrayCollection();
        $this->observationPoints = new ArrayCollection();
        $this->dateCreated = new \DateTime();
        $this->dateModified = new \DateTime();
    }

    /**
     * Set Id
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
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set public
     *
     * @param boolean $public
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
     * Get SoilModels
     */
    public function getSoilModels()
    {
        return $this->soilModels;
    }

    /**
     * Add SoilModel
     *
     * @param SoilModel $soilModel
     * @return $this
     */
    public function addSoilModel(SoilModel $soilModel)
    {
        $this->soilModels[] = $soilModel;

        return $this;
    }

    /**
     * Remove SoilModel
     *
     * @param SoilModel $soilModel
     */
    public function removeSoilModel(SoilModel $soilModel)
    {
        $this->soilModels->removeElement($soilModel);
    }

    /**
     * Set owner
     *
     * @param \AppBundle\Entity\User $owner
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
     * @return \AppBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add Property
     *
     * @param \AppBundle\Entity\Property $property
     * @return $this
     */
    public function addProperty(Property $property)
    {
        $property->setModelObject($this);
        $this->properties[] = $property;

        return $this;
    }

    /**
     * Remove Property
     *
     * @param \AppBundle\Entity\Property $property
     */
    public function removeProperty(Property $property)
    {
        $this->properties->removeElement($property);
    }

    /**
     * Get Properties
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Add observationPoints
     *
     * @param \AppBundle\Entity\ObservationPoint $observationPoints
     * @return $this
     */
    public function addObservationPoint(ObservationPoint $observationPoints)
    {
        $this->observationPoints[] = $observationPoints;
        $observationPoints->addModelObject($this);

        return $this;
    }

    /**
     * Remove observationPoints
     *
     * @param \AppBundle\Entity\ObservationPoint $observationPoints
     */
    public function removeObservationPoint(ObservationPoint $observationPoints)
    {
        $this->observationPoints->removeElement($observationPoints);
        $observationPoints->removeModelObject($this);
    }

    /**
     * Get observationPoints
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getObservationPoints()
    {
        return $this->observationPoints;
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateDateModified()
    {
        $this->dateModified = new \DateTime();
    }

    /**
     * Set name
     *
     * @param string $name
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
     * @return int
     */
    public function getPropertyIds()
    {
        /** @var Property $property */
        foreach ($this->getProperties() as $property)
        {
            $this->propertyIds[] = $property->getId();
        }

        return $this->propertyIds;
    }
}
