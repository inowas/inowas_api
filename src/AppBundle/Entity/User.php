<?php

namespace AppBundle\Entity;

use AppBundle\Model\UserProfileFactory;
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
     * @ORM\OneToOne(targetEntity="UserProfile", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     * @JMS\Expose
     */
    protected $profile;

    protected $ownedSoilModels;

    public function __construct()
    {
        parent::__construct();
        $this->id = Uuid::uuid4();
        $this->profile = UserProfileFactory::create();
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
}
