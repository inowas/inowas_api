<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * AbstractModel
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity()
 * @ORM\Table(name="models")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({  "modflow" = "ModFlowModel" })
 * @JMS\ExclusionPolicy("none")
 */
abstract class AbstractModel
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255)
     * @JMS\Groups({"list", "details", "modeldetails"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @JMS\Groups({"list", "details", "modeldetails"})
     */
    private $description;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownedModels")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     * @JMS\MaxDepth(1)
     * @JMS\Groups({"details", "modeldetails"})
     */
    private $owner;

    /**
     * @var ArrayCollection ModelObject $modelObjects
     *
     * @ORM\ManyToMany(targetEntity="ModelObject", mappedBy="models", cascade={"persist", "remove"})
     **/
    private $modelObjects;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean")
     * @JMS\Groups({"list", "details"})
     */
    private $public;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     * @JMS\Groups({"list", "details"})
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModified", type="datetime")
     * @JMS\Groups({"list", "details"})
     */
    private $dateModified;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->modelObjects = new ArrayCollection();
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
     * @return AbstractModel
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
     * @return AbstractModel
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
     * @return AbstractModel
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
     * @return AbstractModel
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
     * @return AbstractModel
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
     * @return AbstractModel
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
     * Add participant
     *
     * @param \AppBundle\Entity\User $participant
     *
     * @return AbstractModel
     */
    public function addParticipant(\AppBundle\Entity\User $participant)
    {
        $this->participants[] = $participant;

        return $this;
    }

    /**
     * Remove participant
     *
     * @param \AppBundle\Entity\User $participant
     */
    public function removeParticipant(\AppBundle\Entity\User $participant)
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
     * Add soilModelObject
     *
     * @param \AppBundle\Entity\ModelObject $modelObject
     * @return $this
     */
    public function addModelObject(ModelObject $modelObject)
    {
        if (!$modelObject->getModels()->contains($this))
        {
            $modelObject->addModel($this);
        }
        $this->modelObjects[] = $modelObject;

        return $this;
    }

    /**
     * Remove soilModelObject
     *
     * @param \AppBundle\Entity\ModelObject $modelObject
     * @return $this
     */
    public function removeModelObject(ModelObject $modelObject)
    {
        if ($modelObject->getModels()->contains($modelObject))
        {
            $this->modelObjects->removeElement($modelObject);
        }
        $modelObject->removeModel($this);
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
