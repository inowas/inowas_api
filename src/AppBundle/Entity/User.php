<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation as JMS;

/**
 * Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity()
 * @JMS\ExclusionPolicy("all")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var UserProfile
     *
     * @ORM\OneToOne(targetEntity="UserProfile", mappedBy="user", cascade={"persist", "remove"})
     * @JMS\Expose
     */
    protected $profile;

    /**
     * @var ArrayCollection Project $ownedProjects
     *
     * @ORM\OneToMany(targetEntity="Project", mappedBy="owner")
     */
    protected $ownedProjects;

    /**
     * @var ArrayCollection Project $ownedModelObjects
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ModelObject", mappedBy="owner")
     */
    protected $ownedModelObjects;

    /**
     * @var ArrayCollection Project $participatedProjects
     *
     * @ORM\ManyToMany(targetEntity="Project", mappedBy="participants")
     **/
    protected $participatedProjects;

    public function __construct()
    {
        parent::__construct();
        $this->profile = new UserProfile($this);
        $this->ownedProjects = new ArrayCollection();
        $this->ownedModelObjects = new ArrayCollection();
    }


    /**
     * Set profile
     *
     * @param \AppBundle\Entity\UserProfile $profile
     * @return User
     */
    public function setProfile(UserProfile $profile = null)
    {
        $this->profile = $profile;
        $profile->setUser($this);

        return $this;
    }

    /**
     * Get profile
     *
     * @return \AppBundle\Entity\UserProfile 
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Add ownedProjects
     *
     * @param \AppBundle\Entity\Project $ownedProjects
     * @return User
     */
    public function addOwnedProject(Project $ownedProjects)
    {
        $this->ownedProjects[] = $ownedProjects;
        $ownedProjects->setOwner($this);
        return $this;
    }

    /**
     * Remove ownedProjects
     *
     * @param \AppBundle\Entity\Project $ownedProjects
     */
    public function removeOwnedProject(Project $ownedProjects)
    {
        $this->ownedProjects->removeElement($ownedProjects);
    }

    /**
     * Get ownedProjects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOwnedProjects()
    {
        return $this->ownedProjects;
    }

    /**
     * Add ownedModelObjects
     *
     * @param \AppBundle\Entity\ModelObject $ownedModelObjects
     * @return User
     */
    public function addOwnedModelObject(ModelObject $ownedModelObjects)
    {
        $this->ownedModelObjects[] = $ownedModelObjects;
        $ownedModelObjects->setOwner($this);
        return $this;
    }

    /**
     * Remove ownedModelObjects
     *
     * @param \AppBundle\Entity\ModelObject $ownedModelObjects
     */
    public function removeOwnedModelObject(ModelObject $ownedModelObjects)
    {
        $this->ownedModelObjects->removeElement($ownedModelObjects);
    }

    /**
     * Get ownedModelObjects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOwnedModelObjects()
    {
        return $this->ownedModelObjects;
    }

    /**
     * Add participatedProjects
     *
     * @param \AppBundle\Entity\Project $participatedProjects
     * @return User
     */
    public function addParticipatedProject(Project $participatedProjects)
    {
        $this->participatedProjects[] = $participatedProjects;
        $participatedProjects->addParticipant($this);

        return $this;
    }

    /**
     * Remove participatedProjects
     *
     * @param \AppBundle\Entity\Project $participatedProjects
     */
    public function removeParticipatedProject(Project $participatedProjects)
    {
        $this->participatedProjects->removeElement($participatedProjects);
        $participatedProjects->removeParticipant($this);
    }

    /**
     * Get participatedProjects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParticipatedProjects()
    {
        return $this->participatedProjects;
    }
}
