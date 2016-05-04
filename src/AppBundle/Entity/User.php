<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity()
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid")
     * @JMS\Type("string")
     * @JMS\Groups({"list", "projectDetails", "details", "modeldetails", "modelobjectdetails", "modelobjectlist", "soilmodellist", "soilmodeldetails"})
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SoilModel", mappedBy="owner", cascade={"persist", "remove"})
     */
    protected $ownedSoilModels;

    public function __construct()
    {
        parent::__construct();
        $this->id = Uuid::uuid4();
        $this->profile = new UserProfile($this);
        $this->ownedApplications = new ArrayCollection();
        $this->ownedModelObjects = new ArrayCollection();
        $this->ownedModels = new ArrayCollection();
        $this->ownedSoilModels = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
