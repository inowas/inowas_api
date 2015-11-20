<?php

namespace AppBundle\Model;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserInterface;

class ProjectFactory
{
    /**
     * ProjectFactory constructor.
     */
    public function __construct()
    {
        return new Project();
    }

    /**
     * @param UserInterface|null $owner
     * @param bool|false $public
     * @return Project
     */
    public static function setOwnerAndPublic(UserInterface $owner = null, $public = false)
    {
        $project = new Project();
        $project->setOwner($owner);
        $project->setPublic($public);

        return $project;
    }
}