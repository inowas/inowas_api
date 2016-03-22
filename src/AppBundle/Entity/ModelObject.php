<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

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
     * @JMS\Groups({"list", "details"})
     */
    protected $type = 'modelobject';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"list", "details", "layerdetails"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @JMS\Groups({"list", "details", "layerdetails"})
     */
    protected $name;

    /**
     * @var ArrayCollection Project
     *
     * @ORM\ManyToMany(targetEntity="Project", inversedBy="modelObjects")
     * @ORM\JoinTable(name="projects_model_objects")
     **/
    protected $projects;

    /**
     * @var ArrayCollection SoilModel
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\SoilModel", inversedBy="modelObjects", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="soil_model_model_objects")
     **/
    protected $soilModels;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownedModelObjects")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * @var ArrayCollection Property
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Property", mappedBy="modelObject", cascade={"persist", "remove"})
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Property>")
     * @JMS\Groups({"details", "layerdetails"})
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
     */
    protected $observationPoints;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean")
     * @JMS\Groups({"list", "details", "layerdetails"})
     */
    protected $public;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    protected $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModified", type="datetime")
     */
    protected $dateModified;

    /**
     * Constructor
     * @param User $owner
     * @param Project $project
     * @param $public
     */
    public function __construct(User $owner = null, Project $project = null, $public = false)
    {
        $this->owner = $owner;
        $this->public = $public;
        $this->projects = new ArrayCollection();
        $this->soilModels = new ArrayCollection();
        $this->propertyIds = new ArrayCollection();
        if ($project) $this->addProject($project);
        $this->properties = new ArrayCollection();
        $this->observationPoints = new ArrayCollection();
        $this->dateCreated = new \DateTime();
        $this->dateModified = new \DateTime();
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
     * @return ModelObject
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
     * @return ModelObject
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
     * @return ModelObject
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
     * Add projects
     *
     * @param \AppBundle\Entity\Project $projects
     * @return ModelObject
     */
    public function addProject(Project $projects)
    {
        $this->projects[] = $projects;
        $projects->addModelObject($this);

        return $this;
    }

    /**
     * Remove projects
     *
     * @param \AppBundle\Entity\Project $projects
     */
    public function removeProject(Project $projects)
    {
        $this->projects->removeElement($projects);
        $projects->removeModelObject($this);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjects()
    {
        return $this->projects;
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
     * @return ModelObject
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
     * @return ModelObject
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
     * @return ModelObject
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
     * @return ModelObject
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
