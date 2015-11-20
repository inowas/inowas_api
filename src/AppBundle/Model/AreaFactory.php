<?php

namespace AppBundle\Model;

use AppBundle\Entity\Area;
use AppBundle\Entity\Project;
use FOS\UserBundle\Model\UserInterface;

class AreaFactory
{
    /**
     * GeologicalPointFactory constructor.
     */
    public function __construct()
    {
        return new Area();
    }

    public static function setOwnerProjectNameAndPublic(UserInterface $owner = null, Project $project = null, $name = "", $public = false)
    {
        $area = new Area();
        $area->setOwner($owner);
        $area->addProject($project);
        $area->setName($name);
        $area->setPublic($public);

        return $area;
    }
}