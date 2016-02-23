<?php

namespace AppBundle\Model;

use AppBundle\Entity\Area;
use AppBundle\Entity\AreaType;
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

    /**
     * @return Area
     */
    public static function create()
    {
        return new Area();
    }

    /**
     * @param UserInterface|null $owner
     * @param Project|null $project
     * @param string $name
     * @param AreaType|null $type
     * @param bool $public
     * @return Area
     */
    public static function setOwnerProjectNameTypeAndPublic(UserInterface $owner = null, Project $project = null, $name = "", AreaType $type=null, $public = false)
    {
        $area = new Area();
        $area->setOwner($owner);
        $area->addProject($project);
        $area->setName($name);
        $area->setAreaType($type);
        $area->setPublic($public);

        return $area;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'Area';
    }
}