<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ProjectRepository")
 */
class Project
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownedProjects")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var ArrayCollection User
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="participatedProjects")
     */
    private $participants;

    /**
     * @var ArrayCollection ModelObject
     *
     * @ORM\ManyToMany(targetEntity="ModelObject", mappedBy="projects")
     **/
    protected $modelObjects;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="features", type="object")
     */
    private $features;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean")
     */
    private $public;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->modelObjects = new ArrayCollection();
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
     * Set owner
     *
     * @param \stdClass $owner
     * @return Project
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \stdClass 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set participants
     *
     * @param \stdClass $participants
     * @return Project
     */
    public function setParticipants($participants)
    {
        $this->participants = $participants;

        return $this;
    }

    /**
     * Get participants
     *
     * @return \stdClass 
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * Set features
     *
     * @param \stdClass $features
     * @return Project
     */
    public function setFeatures($features)
    {
        $this->features = $features;

        return $this;
    }

    /**
     * Get features
     *
     * @return \stdClass 
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * Set public
     *
     * @param boolean $public
     * @return Project
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
     * Add participants
     *
     * @param \AppBundle\Entity\User $participants
     * @return Project
     */
    public function addParticipant(User $participants)
    {
        $participants->addParticipatedProject($this);
        $this->participants[] = $participants;

        return $this;
    }

    /**
     * Remove participants
     *
     * @param \AppBundle\Entity\User $participants
     */
    public function removeParticipant(User $participants)
    {
        $participants->removeParticipatedProject($this);
        $this->participants->removeElement($participants);
    }

    /**
     * Add modelObjects
     *
     * @param \AppBundle\Entity\ModelObject $modelObjects
     * @return Project
     */
    public function addModelObject(ModelObject $modelObjects)
    {
        $modelObjects->addProject($this);
        $this->modelObjects[] = $modelObjects;

        return $this;
    }

    /**
     * Remove modelObjects
     *
     * @param \AppBundle\Entity\ModelObject $modelObjects
     */
    public function removeModelObject(ModelObject $modelObjects)
    {
        $modelObjects->removeProject($this);
        $this->modelObjects->removeElement($modelObjects);
    }

    /**
     * Get modelObjects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getModelObjects()
    {
        return $this->modelObjects;
    }
}
