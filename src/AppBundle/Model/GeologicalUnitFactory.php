<?php

namespace AppBundle\Model;

use AppBundle\Entity\GeologicalUnit;
use AppBundle\Entity\Project;
use FOS\UserBundle\Model\UserInterface;

class GeologicalUnitFactory
{

    private final function __construct(){}

    /**
     * @return GeologicalUnit
     */
    public static function create()
    {
        return new GeologicalUnit();
    }

    /**
     * @param UserInterface|null $owner
     * @param string $name
     * @param bool $public
     * @return GeologicalUnit
     */
    public static function setOwnerNameAndPublic(UserInterface $owner = null, $name = "", $public = false)
    {
        $gu = new GeologicalUnit();
        $gu->setOwner($owner);
        $gu->setName($name);
        $gu->setPublic($public);

        return $gu;
    }
}