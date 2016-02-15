<?php

namespace AppBundle\Model;

use AppBundle\Entity\User;

class UserFactory
{
    public static function create()
    {
        return new User();
    }
}