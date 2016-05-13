<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * Profile
 *
 * @ORM\Table(name="user_profiles"))
 * @ORM\Entity()
 */
class UserProfile
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true)
     * @JMS\Type("string")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    protected $firstName = "";

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    protected $lastName = "";

    /**
     * UserProfile constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
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
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param null $firstName
     * @return $this
     */
    public function setFirstName($firstName = null)
    {
        if (is_null($firstName))
        {
            $firstName = "";
        }
        $this->firstName = $firstName;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param null $lastName
     * @return $this
     */
    public function setLastName($lastName = null)
    {
        if (is_null($lastName))
        {
            $lastName = "";
        }
        $this->lastName = $lastName;

        return $this;
    }
}
