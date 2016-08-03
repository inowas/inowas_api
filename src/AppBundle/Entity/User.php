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

    /**
     * @var string
     *
     * @ORM\Column(name="api_key", type="uuid", length=255)
     */
    protected $apiKey;

    public function __construct()
    {
        parent::__construct();
        $this->id = Uuid::uuid4();
        $this->apiKey = Uuid::uuid4();
        $this->profile = UserProfileFactory::create();
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
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
     * Get apiKey
     *
     * @return Uuid
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     *
     */
    public function generateNewApiKey(){
        $this->apiKey = Uuid::uuid4();
    }
}
