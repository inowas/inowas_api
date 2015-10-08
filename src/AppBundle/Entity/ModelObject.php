<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Feature
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ModelObjectRepository")
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
     * @ORM\Column(name="featureType", type="string", length=255)
     */
    private $featureType;

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
     * Set featureType
     *
     * @param string $featureType
     * @return Feature
     */
    public function setFeatureType($featureType)
    {
        $this->featureType = $featureType;

        return $this;
    }

    /**
     * Get featureType
     *
     * @return string 
     */
    public function getFeatureType()
    {
        return $this->featureType;
    }

    /**
     * Set owner
     *
     * @param string $owner
     * @return Feature
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
     * @return Feature
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
     * @return Feature
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
     * @return Feature
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
     * @return Feature
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
        $this->projects = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add projects
     *
     * @param \AppBundle\Entity\Project $projects
     * @return ModelObject
     */
    public function addProject(\AppBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;

        return $this;
    }

    /**
     * Remove projects
     *
     * @param \AppBundle\Entity\Project $projects
     */
    public function removeProject(\AppBundle\Entity\Project $projects)
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
