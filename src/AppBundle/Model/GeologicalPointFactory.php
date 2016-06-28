<?php

namespace AppBundle\Model;

use AppBundle\Entity\GeologicalPoint;
use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserInterface;

class GeologicalPointFactory
{

    private final function __construct(){}

    /**
     * @return GeologicalPoint
     */
    public static function create()
    {
        return new GeologicalPoint();
    }

    /**
     * @param User|null $owner
     * @param bool|false $public
     * @return GeologicalPoint
     */
    public static function setOwnerAndPublic(User $owner = null, $public = false)
    {
        $geologicalPoint = new GeologicalPoint();
        $geologicalPoint->setOwner($owner);
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
    public static function setOwnerProjectNameAndPoint(UserInterface $owner = null, $name = "", Point $point = null, $public = false)
    {
        $geologicalPoint = new GeologicalPoint();
        $geologicalPoint->setOwner($owner);
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