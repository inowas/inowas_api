<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Project
 *
 * @ORM\Table(name="projects")
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
     * @JMS\Groups({"list", "details"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255)
     * @JMS\Groups({"list", "details"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @JMS\Groups({"list", "details"})
     */
    private $description;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownedProjects")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="cascade")
     * @JMS\MaxDepth(2)
     * @JMS\Groups({"list", "details"})
     */
    private $owner;

    /**
     * @var ArrayCollection User
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="participatedProjects")
     * @ORM\JoinTable(name="users_participated_projects")
     * @JMS\MaxDepth(1)
     * @JMS\Groups({"list", "details"})
     */
    private $participants;

    /**
     * @var ArrayCollection ModelObject
     *
     * @ORM\ManyToMany(targetEntity="ModelObject", mappedBy="projects", cascade={"all"})
     * @JMS\MaxDepth(2)
     * @JMS\Groups({"list", "details"})
     **/
    protected $modelObjects;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean")
     * @JMS\Groups({"list", "details"})
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
     * Set owner
     *
     * @param \AppBundle\Entity\User $owner
     * @return Project
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
     * Add participants
     *
     * @param \AppBundle\Entity\User $participants
     * @return Project
     */
    public function addParticipant(User $participants)
    {
        $this->participants[] = $participants;
        $participants->addParticipatedProject($this);

        return $this;
    }

    /**
     * Remove participants
     *
     * @param \AppBundle\Entity\User $participants
     */
    public function removeParticipant(User $participants)
    {
        $this->participants->removeElement($participants);
        $participants->removeParticipatedProject($this);
    }

    /**
     * Get participants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * Add modelObjects
     *
     * @param \AppBundle\Entity\ModelObject $modelObjects
     * @return Project
     */
    public function addModelObject(ModelObject $modelObjects)
    {
        $this->modelObjects[] = $modelObjects;
        if (!in_array($this, $modelObjects->getProjects()->toArray()))
        {
            $modelObjects->addProject($this);
        }

        return $this;
    }

    /**
     * Remove modelObjects
     *
     * @param \AppBundle\Entity\ModelObject $modelObjects
     */
    public function removeModelObject(ModelObject $modelObjects)
    {
        $this->modelObjects->removeElement($modelObjects);
        if (in_array($this, $modelObjects->getProjects()->toArray()))
        {
            $modelObjects->removeProject($this);
        }
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

    /**
     * Set name
     *
     * @param string $name
     * @return Project
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
     * @return Project
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
}
