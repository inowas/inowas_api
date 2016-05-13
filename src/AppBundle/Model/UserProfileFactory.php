<?php

namespace AppBundle\Model;

use AppBundle\Entity\UserProfile;

class UserProfileFactory
{
    public static function create()
    {
        return new UserProfile();
    }

    public static function createTestUserProfile($testCase)
    {
        $userProfile = new UserProfile();
        $userProfile->setFirstName($testCase.'FirstName')
            ->setLastName($testCase.'LastName');

        return $userProfile;
    }
}