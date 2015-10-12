<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * Users
 *
 * @ORM\Table(name="inowas_user")
 * @ORM\Entity()
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var UserProfile
     *
     * @ORM\OneToOne(targetEntity="UserProfile", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $profile;

    /**
     * @var ArrayCollection Project $ownedProjects
     *
     * @ORM\OneToMany(targetEntity="Project", mappedBy="owner")
     */
    protected $ownedProjects;

    /**
     * @var ArrayCollection Project $participatedProjects
     *
     * @ORM\ManyToMany(targetEntity="Project", mappedBy="participants")
     **/
    protected $participatedProjects;


    public function __construct()
    {
        parent::__construct();
        $this->profile = new UserProfile();
        $this->ownedProjects = new ArrayCollection();
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
     * @param Project $ownedProjects
     * @return User
     */
    public function addOwnedProject(Project $ownedProjects)
    {
        $ownedProjects->addParticipant($this);
        $this->ownedProjects[] = $ownedProjects;

        return $this;
    }

    /**
     * Remove ownedProjects
     *
     * @param Project $ownedProjects
     */
    public function removeOwnedProject(Project $ownedProjects)
    {
        $ownedProjects->removeParticipant($this);
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
     * Add participatedProjects
     *
     * @param Project $participatedProjects
     * @return User
     */
    public function addParticipatedProject(Project $participatedProjects)
    {
        $participatedProjects->addParticipant($this);
        $this->participatedProjects[] = $participatedProjects;
        return $this;
    }

    /**
     * Remove participatedProjects
     *
     * @param Project $participatedProjects
     */
    public function removeParticipatedProject(Project $participatedProjects)
    {
        $participatedProjects->removeParticipant($this);
        $this->participatedProjects->removeElement($participatedProjects);
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
