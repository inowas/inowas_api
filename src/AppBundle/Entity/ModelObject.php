<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ModelObject
 *
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({  "soilprofile" = "SoilProfile",
 *                          "soilprofilelayer" = "SoilProfileLayer",
 *                          "layer" = "Layer",
 *                          "area" = "Area",
 *                          "observationpoint" = "ObservationPoint",
 *                          "raster" = "Raster",
 *                          "stream" = "Stream",
 *                          "lake" = "Lake"
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
     **/
    private $projects;

    /**
     * @var string
     *
     * @ORM\Column(name="ModelObjectType", type="string", length=255)
     */
    private $modelObjectType;

    /**
     * @var string
     *
     * @ORM\Column(name="owner", type="string", length=255)
     */
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="property", type="string", length=255)
     */
    private $property;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set project
     *
     * @param string $projects
     * @return ModelObject
     */
    public function setProject($projects)
    {
        $this->projects = $projects;

        return $this;
    }

    /**
     * Get projects
     *
     * @return string 
     */
    public function getProject()
    {
        return $this->projects;
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
     * Get ModelObjectType
     *
     * @return string 
     */
    public function getModelObjectType()
    {
        return $this->modelObjectType;
    }

    /**
     * Set owner
     *
     * @param string $owner
     * @return ModelObject
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return string 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set property
     *
     * @param string $property
     * @return ModelObject
     */
    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return string 
     */
    public function getProperty()
    {
        return $this->property;
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
     * Constructor
     */
    public function __construct()
    {
        $this->projects = new ArrayCollection();
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
}
