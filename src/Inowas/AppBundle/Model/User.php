<?php

namespace Inowas\AppBundle\Model;

use FOS\UserBundle\Model\User as BaseUser;
use Ramsey\Uuid\Uuid;

class User extends BaseUser
{
    /** @var  Uuid */
    protected $id;

    /** @var  string */
    protected $name;

    /** @var array */
    protected $profile;

    /** @var  string */
    protected $apiKey;

    public function __construct()
    {
        parent::__construct();
        $this->id = Uuid::uuid4();
        $this->apiKey = Uuid::uuid4()->toString();
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function renewApiKey(): void
    {
        $this->apiKey = Uuid::uuid4()->toString();
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        if (null === $this->name){
            $this->name = '';
        }
        return $this->name;
    }

    public function getProfile(): UserProfile
    {
        if (null === $this->profile) {
            return UserProfile::create();
        }

        return UserProfile::fromArray($this->profile);
    }

    /**
     * @param UserProfile $profile
     */
    public function setProfile(UserProfile $profile): void
    {
        $this->profile = $profile->toArray();
    }
}
