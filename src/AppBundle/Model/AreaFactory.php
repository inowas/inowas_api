<?php

namespace AppBundle\Model;

use AppBundle\Entity\Area;
use AppBundle\Entity\AreaType;
use FOS\UserBundle\Model\UserInterface;

class AreaFactory
{
    private final function __construct(){}

    /**
     * @return Area
     */
    public static function create()
    {
        return new Area();
    }

    /**
     * @param UserInterface|null $owner
     * @param string $name
     * @param AreaType|null $type
     * @param bool $public
     * @return Area
     */
    public static function setOwnerProjectNameTypeAndPublic(UserInterface $owner = null, $name = "", AreaType $type=null, $public = false)
    {
        $area = new Area();
        $area->setOwner($owner);
        $area->setName($name);
        $area->setAreaType($type);
        $area->setPublic($public);

        return $area;
    }

    /**
     * @param UserInterface|null $owner
     * @param string $name
     * @param AreaType|null $type
     * @param bool $public
     * @return Area
     */
    public static function setOwnerNameTypeAndPublic(UserInterface $owner = null, $name = "", AreaType $type=null, $public = false)
    {
        $area = new Area();
        $area->setOwner($owner);
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