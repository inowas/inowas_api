<?php

namespace AppBundle\Model;

use AppBundle\Entity\GeologicalPoint;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Model\Point;
use FOS\UserBundle\Model\UserInterface;

class GeologicalPointFactory
{
    /**
     * GeologicalPointFactory constructor.
     */
    public function __construct()
    {
        return new GeologicalPoint();
    }

    /**
     * @return GeologicalPoint
     */
    public static function create()
    {
        return new GeologicalPoint();
    }

    /**
     * @param User|null $owner
     * @param Project $project
     * @param bool|false $public
     * @return GeologicalPoint
     */
    public static function setOwnerAndPublic(User $owner = null, Project $project = null, $public = false)
    {
        $geologicalPoint = new GeologicalPoint();
        $geologicalPoint->setOwner($owner);
        $geologicalPoint->addProject($project);
        $geologicalPoint->setPublic($public);

        return $geologicalPoint;
    }

    /**
     * @param UserInterface|null $owner
     * @param Project|null $project
     * @param string $name
     * @param Point|null $point
     * @param bool|false $public
     * @return GeologicalPoint
     */
    public static function setOwnerProjectNameAndPoint(UserInterface $owner = null, Project $project = null, $name = "", Point $point = null, $public = false)
    {
        $geologicalPoint = new GeologicalPoint();
        $geologicalPoint->setOwner($owner);
        $geologicalPoint->addProject($project);
        $geologicalPoint->setName($name);
        $geologicalPoint->setPoint($point);
        $geologicalPoint->setPublic($public);

        return $geologicalPoint;
    }

    /**
     * @param UserInterface|null $owner
     * @param string $name
     * @param Point|null $point
     * @param bool|false $public
     * @return GeologicalPoint
     */
    public static function setOwnerNameAndPoint(UserInterface $owner = null, $name = "", Point $point = null, $public = false)
    {
        $geologicalPoint = new GeologicalPoint();
        $geologicalPoint->setOwner($owner);
        $geologicalPoint->setName($name);
        $geologicalPoint->setPoint($point);
        $geologicalPoint->setPublic($public);

        return $geologicalPoint;
    }
}