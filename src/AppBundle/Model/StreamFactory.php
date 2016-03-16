<?php

namespace AppBundle\Model;

use AppBundle\Entity\Project;
use AppBundle\Entity\Stream;
use FOS\UserBundle\Model\UserInterface;

class StreamFactory
{
    /**
     * GeologicalPointFactory constructor.
     */
    public function __construct()
    {
        return new Stream();
    }

    public static function setOwnerProjectNameAndPublic(UserInterface $owner = null, Project $project = null, $name = "", $public = false)
    {
        $stream = new Stream();
        $stream->setOwner($owner);
        $stream->addProject($project);
        $stream->setName($name);
        $stream->setPublic($public);

        return $stream;
    }

    public static function setOwnerNameAndPublic(UserInterface $owner = null, $name = "", $public = false)
    {
        $stream = new Stream();
        $stream->setOwner($owner);
        $stream->setName($name);
        $stream->setPublic($public);

        return $stream;
    }
}