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
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"projectDetails","details"})
     */
    protected $id;

    /**
     * @var UserProfile
     *
     * @ORM\OneToOne(targetEntity="UserProfile", orphanRemoval=true, cascade={"persist", "detach", "remove"})
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     * @JMS\Expose
     */
    protected $profile;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Application", mappedBy="owner", cascade={"persist", "remove"})
     */
    protected $ownedApplications;

    /**
     * @var ArrayCollection Project
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ModelObject", mappedBy="owner")
     */
    protected $ownedModelObjects;

    /**
     * @var ArrayCollection AbstractModel
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AbstractModel", mappedBy="owner", cascade={"persist", "remove"})
     */
    protected $ownedModels;

    /**
     * @var ArrayCollection Project
     *
     * @ORM\OneToMany(targetEntity="Project", mappedBy="owner", cascade={"persist", "remove"})
     */
    protected $ownedProjects;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SoilModel", mappedBy="owner", cascade={"persist", "remove"})
     */
    protected $ownedSoilModels;

    /**
     * @var ArrayCollection Project $participatedProjects
     *
     * @ORM\ManyToMany(targetEntity="Project", mappedBy="participants", cascade={"persist", "remove"})
     **/
    protected $participatedProjects;

    public function __construct()
    {
        parent::__construct();
        $this->profile = new UserProfile($this);
        $this->ownedApplications = new ArrayCollection();
        $this->ownedModelObjects = new ArrayCollection();
        $this->ownedModels = new ArrayCollection();
        $this->ownedProjects = new ArrayCollection();
        $this->ownedSoilModels = new ArrayCollection();
        $this->participatedProjects = new ArrayCollection();
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

    /**
     * Add ownedApplication
     *
     * @param \AppBundle\Entity\Application $ownedApplication
     *
     * @return User
     */
    public function addOwnedApplication(\AppBundle\Entity\Application $ownedApplication)
    {
        $this->ownedApplications[] = $ownedApplication;

        return $this;
    }

    /**
     * Remove ownedApplication
     *
     * @param \AppBundle\Entity\Application $ownedApplication
     */
    public function removeOwnedApplication(\AppBundle\Entity\Application $ownedApplication)
    {
        $this->ownedApplications->removeElement($ownedApplication);
    }

    /**
     * Get ownedApplications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOwnedApplications()
    {
        return $this->ownedApplications;
    }

    /**
     * Add ownedModel
     *
     * @param \AppBundle\Entity\AbstractModel $ownedModel
     *
     * @return User
     */
    public function addOwnedModel(\AppBundle\Entity\AbstractModel $ownedModel)
    {
        $this->ownedModels[] = $ownedModel;

        return $this;
    }

    /**
     * Remove ownedModel
     *
     * @param \AppBundle\Entity\AbstractModel $ownedModel
     */
    public function removeOwnedModel(\AppBundle\Entity\AbstractModel $ownedModel)
    {
        $this->ownedModels->removeElement($ownedModel);
    }

    /**
     * Get ownedModels
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOwnedModels()
    {
        return $this->ownedModels;
    }

    /**
     * Add ownedSoilModel
     *
     * @param \AppBundle\Entity\SoilModel $ownedSoilModel
     *
     * @return User
     */
    public function addOwnedSoilModel(\AppBundle\Entity\SoilModel $ownedSoilModel)
    {
        $this->ownedSoilModels[] = $ownedSoilModel;

        return $this;
    }

    /**
     * Remove ownedSoilModel
     *
     * @param \AppBundle\Entity\SoilModel $ownedSoilModel
     */
    public function removeOwnedSoilModel(\AppBundle\Entity\SoilModel $ownedSoilModel)
    {
        $this->ownedSoilModels->removeElement($ownedSoilModel);
    }

    /**
     * Get ownedSoilModels
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOwnedSoilModels()
    {
        return $this->ownedSoilModels;
    }
}
