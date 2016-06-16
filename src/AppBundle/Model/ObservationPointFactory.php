<?php

namespace AppBundle\Model;

use AppBundle\Entity\ObservationPoint;
use FOS\UserBundle\Model\UserInterface;

class ObservationPointFactory
{
    public function __construct()
    {
        return new ObservationPoint();
    }

    public static function create()
    {
        return new ObservationPoint();
    }

    /**
     * @param UserInterface|null $owner
     * @param string $name
     * @param Point|null $point
     * @param bool|false $public
     * @return ObservationPoint
     */
    public static function setOwnerNameAndPoint(UserInterface $owner = null, $name = "", Point $point = null, $public = false)
    {
        $op = new ObservationPoint();
        $op->setOwner($owner);
        $op->setName($name);
        $op->setGeometry($point);
        $op->setPublic($public);

        return $op;
    }
}