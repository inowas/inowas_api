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

    /**
     * @param $testCase
     * @return UserProfile
     */
    public static function createTestUserProfile($testCase)
    {
        $userProfile = new UserProfile();
        $userProfile->setFirstName($testCase.'FirstName')
            ->setLastName($testCase.'LastName');

        return $userProfile;
    }
}