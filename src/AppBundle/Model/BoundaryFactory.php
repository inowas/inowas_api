<?php

namespace AppBundle\Model;

use AppBundle\Entity\Boundary;
use AppBundle\Entity\Project;
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

    public static function setOwnerProjectNameAndPublic(UserInterface $owner = null, Project $project = null, $name = "", $public = false)
    {
        $boundary = new Boundary();
        $boundary->setOwner($owner);
        $boundary->addProject($project);
        $boundary->setName($name);
        $boundary->setPublic($public);

        return $boundary;
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