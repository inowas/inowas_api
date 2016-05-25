<?php

namespace AppBundle\Model;

use AppBundle\Entity\Boundary;
use FOS\UserBundle\Model\UserInterface;

class BoundaryFactory
{
    /**
     * GeologicalPointFactory constructor.
     */
    public function __construct()
    {
        return new Boundary();
    }

    public static function create()
    {
        return new Boundary();
    }

    public static function setOwnerNameAndPublic(UserInterface $owner = null, $name = "", $public = false)
    {
        $boundary = new Boundary();
        $boundary->setOwner($owner);
        $boundary->setName($name);
        $boundary->setPublic($public);

        return $boundary;
    }
}