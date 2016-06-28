<?php

namespace AppBundle\Model;

use AppBundle\Entity\GeologicalLayer;
use FOS\UserBundle\Model\UserInterface;

class GeologicalLayerFactory
{

    private final function __construct(){}

    /**
     * @return GeologicalLayer
     */
    public static function create()
    {
        return new GeologicalLayer();
    }

    /**
     * @param UserInterface|null $owner
     * @param string $name
     * @param bool $public
     * @return GeologicalLayer
     */
    public static function setOwnerNameAndPublic(UserInterface $owner = null, $name = "", $public = false)
    {
        $geologicalLayer = new GeologicalLayer();
        $geologicalLayer->setOwner($owner);
        $geologicalLayer->setName($name);
        $geologicalLayer->setPublic($public);

        return $geologicalLayer;
    }
}