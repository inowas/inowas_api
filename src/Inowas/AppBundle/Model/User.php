<?php

namespace Inowas\AppBundle\Model;

use FOS\UserBundle\Model\User as BaseUser;
use Ramsey\Uuid\Uuid;

class User extends BaseUser
{
    /** @var  Uuid */
    protected $id;

    /** @var  Uuid */
    protected $apiKey;

    public function __construct()
    {
        parent::__construct();
        $this->id = Uuid::uuid4();
        $this->apiKey = Uuid::uuid4()->toString();
    }

    /**
     * @return Uuid
     */
    public function getApiKey(): Uuid
    {
        return $this->apiKey;
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }
}
