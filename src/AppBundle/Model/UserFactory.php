<?php

namespace AppBundle\Model;

use AppBundle\Entity\User;

class UserFactory
{
    private final function __construct(){}

    public static function create(){
        return new User();
    }

    public static function createTestUser($testCase)
    {
        $user = new User();
        $user->setUsername($testCase.'TestUser'.rand(1000000,10000000000))
            ->setEmail($testCase.'TestUser'.rand(1000000,10000000000).'@inowas.com.')
            ->setPlainPassword($testCase.'TestUserPassWord')
            ->setEnabled(true);

        return $user;
    }
}
