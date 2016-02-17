<?php

namespace AppBundle\Entity;

use AppBundle\Model\CalculationFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Project
 *
 * @ORM\HasLifecycleCallbacks()
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
     * @JMS\Groups({"projectList", "projectDetails"})
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownedProjects")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     * @JMS\MaxDepth(3)
     * @JMS\Groups({"projectDetails"})
     */
    private $owner;

    /**
     * @var ArrayCollection User
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="participatedProjects")
     * @ORM\JoinTable(name="users_participated_projects")
     * @JMS\Groups({"projectDetails"})
     */
    private $participants;

    /**
     * @var ArrayCollection ModelObject $modelObjects
     *
     * @ORM\ManyToMany(targetEntity="ModelObject", mappedBy="projects", cascade={"persist", "remove"})
     * @JMS\Type("ArrayCollection<AppBundle\Entity\ModelObject>")
     **/
    private $modelObjects;

    /**
     * @var Calculation
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Calculation", inversedBy="project", cascade={"persist", "remove"})
     */
    private $calculation;

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
     * Constructor
     */
    public function __construct()
    {
        $this->calculation = CalculationFactory::create();
        $this->calculation->setProject($this);
        $this->participants = new ArrayCollection();
        $this->modelObjects = new ArrayCollection();
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
     * @param \AppBundle\Entity\User $participant
     * @return Project
     */
    public function addParticipant(User $participant)
    {
        $this->participants[] = $participant;

        return $this;
    }

    /**
     * Remove participants
     *
     * @param \AppBundle\Entity\User $participant
     */
    public function removeParticipant(User $participant)
    {
        $this->participants->removeElement($participant);
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
     * @ORM\PreUpdate
     */
    public function updateDateModified()
    {
        $this->dateModified = new \DateTime();
    }

    /**
     * Set calculation
     *
     * @param \AppBundle\Entity\Calculation $calculation
     *
     * @return Project
     */
    public function setCalculation(\AppBundle\Entity\Calculation $calculation = null)
    {
        $this->calculation = $calculation;

        return $this;
    }

    /**
     * Get calculation
     *
     * @return \AppBundle\Entity\Calculation
     */
    public function getCalculation()
    {
        return $this->calculation;
    }
}
