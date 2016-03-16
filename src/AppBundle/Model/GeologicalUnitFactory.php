<?php

namespace AppBundle\Model;

use AppBundle\Entity\GeologicalUnit;
use AppBundle\Entity\Project;
use FOS\UserBundle\Model\UserInterface;

class GeologicalUnitFactory
{
    public function __construct()
    {
        return new GeologicalUnit();
    }

    public static function create()
    {
        return new GeologicalUnit();
    }

    public static function setOwnerProjectNameAndPublic(UserInterface $owner = null, Project $project = null, $name = "", $public = false)
    {
        $gu = new GeologicalUnit();
        $gu->setOwner($owner);
        $gu->addProject($project);
        $gu->setName($name);
        $gu->setPublic($public);

        return $gu;
    }

    public static function setOwnerNameAndPublic(UserInterface $owner = null, $name = "", $public = false)
    {
        $gu = new GeologicalUnit();
        $gu->setOwner($owner);
        $gu->setName($name);
        $gu->setPublic($public);

        return $gu;
    }
}