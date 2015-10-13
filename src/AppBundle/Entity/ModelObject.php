<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ModelObject
 *
 * @ORM\Entity
 * @ORM\Table(name="inowas_model_object")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({  "soilprofile" = "SoilProfile",
 *                          "soilprofilelayer" = "SoilProfileLayer",
 *                          "layer" = "Layer",
 *                          "observationpoint" = "ObservationPoint",
 *                          "stream" = "Stream",
 *                          "area" = "Area"
 * })
 */

class ModelObject
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ArrayCollection Project
     *
     * @ORM\ManyToMany(targetEntity="Project", inversedBy="modelObjects")
     * @ORM\JoinTable(name="inowas_projects_model_objects")
     **/
    private $projects;

    /**
     * @var string
     *
     * @ORM\Column(name="ModelObjectType", type="string", length=255)
     */
    private $modelObjectType;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownedModelObjects")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var ArrayCollection ModelObjectProperty
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\ModelObjectProperty", inversedBy="modelObjects")
     * @ORM\JoinTable(name="inowas_model_object_model_object_property")
     */
    private $modelObjectProperties;

    /**
     * @var ArrayCollection ObservationPoint
     *
     * @ORM\ManyToMany(targetEntity="ObservationPoint", inversedBy="modelObjects")
     * @ORM\JoinTable(name="inowas_model_object_observation_point")
     */
    private $observationPoints;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean")
     */
    private $public;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModified", type="datetime")
     */
    private $dateModified;
    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->modelObjectProperties = new ArrayCollection();
        $this->observationPoints = new ArrayCollection();
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
     * Set modelObjectType
     *
     * @param string $modelObjectType
     * @return ModelObject
     */
    public function setModelObjectType($modelObjectType)
    {
        $this->modelObjectType = $modelObjectType;

        return $this;
    }

    /**
     * Get modelObjectType
     *
     * @return string 
     */
    public function getModelObjectType()
    {
        return $this->modelObjectType;
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
     * Add modelObjectProperties
     *
     * @param \AppBundle\Entity\ModelObjectProperty $modelObjectProperties
     * @return ModelObject
     */
    public function addModelObjectProperty(ModelObjectProperty $modelObjectProperties)
    {
        $this->modelObjectProperties[] = $modelObjectProperties;
        $modelObjectProperties->addModelObject($this);

        return $this;
    }

    /**
     * Remove modelObjectProperties
     *
     * @param \AppBundle\Entity\ModelObjectProperty $modelObjectProperties
     */
    public function removeModelObjectProperty(ModelObjectProperty $modelObjectProperties)
    {
        $this->modelObjectProperties->removeElement($modelObjectProperties);
        $modelObjectProperties->removeModelObject($this);
    }

    /**
     * Get modelObjectProperties
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getModelObjectProperties()
    {
        return $this->modelObjectProperties;
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
}
