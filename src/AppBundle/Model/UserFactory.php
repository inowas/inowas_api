<?php

namespace AppBundle\Model;

use AppBundle\Entity\User;

class UserFactory
{
    public static function create()
    {
        return new User();
    }

    public static function createTestUser($testCase)
    {
        $user = new User();
        $user->setUsername($testCase.'TestUser')
            ->setEmail($testCase.'TestUser@inowas.com.')
            ->setPassword($testCase.'TestUserPassWord')
            ->setEnabled(true);

        return $user;
    }
}