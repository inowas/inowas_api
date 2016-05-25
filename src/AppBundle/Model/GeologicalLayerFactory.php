<?php

namespace AppBundle\Model;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\Project;
use FOS\UserBundle\Model\UserInterface;

class GeologicalLayerFactory
{
    /**
     * GeologicalPointFactory constructor.
     */
    public function __construct()
    {
        return new GeologicalLayer();
    }

    public static function create()
    {
        return new GeologicalLayer();
    }

    public static function setOwnerProjectNameAndPublic(UserInterface $owner = null, Project $project = null, $name = "", $public = false)
    {
        $geologicalLayer = new GeologicalLayer();
        $geologicalLayer->setOwner($owner);
        $geologicalLayer->addProject($project);
        $geologicalLayer->setName($name);
        $geologicalLayer->setPublic($public);

        return $geologicalLayer;
    }

    public static function setOwnerNameAndPublic(UserInterface $owner = null, $name = "", $public = false)
    {
        $geologicalLayer = new GeologicalLayer();
        $geologicalLayer->setOwner($owner);
        $geologicalLayer->setName($name);
        $geologicalLayer->setPublic($public);

        return $geologicalLayer;
    }
}