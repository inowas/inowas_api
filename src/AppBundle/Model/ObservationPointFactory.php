<?php

namespace AppBundle\Model;

use AppBundle\Entity\ObservationPoint;
use AppBundle\Entity\Project;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use FOS\UserBundle\Model\UserInterface;

class ObservationPointFactory
{
    /**
     * GeologicalPointFactory constructor.
     */
    public function __construct()
    {
        return new ObservationPoint();
    }

    /**
     * @param UserInterface|null $owner
     * @param Project|null $project
     * @param string $name
     * @param bool|false $public
     * @return ObservationPoint
     */
    public static function setOwnerProjectNameAndPublic(UserInterface $owner = null, Project $project = null, $name = "", $public = false)
    {
        $op = new ObservationPoint();
        $op->setOwner($owner);
        $op->addProject($project);
        $op->setName($name);
        $op->setPublic($public);

        return $op;
    }

    /**
     * @param UserInterface|null $owner
     * @param Project|null $project
     * @param string $name
     * @param Point|null $point
     * @param bool|false $public
     * @return ObservationPoint
     */
    public static function setOwnerProjectNameAndPoint(UserInterface $owner = null, Project $project = null, $name = "", Point $point = null, $public = false)
    {
        $op = new ObservationPoint();
        $op->setOwner($owner);
        $op->addProject($project);
        $op->setName($name);
        $op->setPoint($point);
        $op->setPublic($public);

        return $op;
    }
}