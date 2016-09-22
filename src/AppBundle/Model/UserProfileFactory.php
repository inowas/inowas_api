<?php

namespace AppBundle\Model;

use AppBundle\Entity\UserProfile;

class UserProfileFactory
{

    private final function __construct(){}

    /**
     * @return UserProfile
     */
    public static function create()
    {
        return new UserProfile();
    }
}
