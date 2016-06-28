<?php

namespace AppBundle\Model;

use AppBundle\Entity\StreamBoundary;
use FOS\UserBundle\Model\UserInterface;

class StreamBoundaryFactory
{

    private final function __construct(){}

    /**
     * @return StreamBoundary
     */
    public static function create()
    {
        return new StreamBoundary();
    }

    /**
     * @param UserInterface|null $owner
     * @param string $name
     * @param bool $public
     * @return StreamBoundary
     */
    public static function setOwnerNameAndPublic(UserInterface $owner = null, $name = "", $public = false)
    {
        $stream = new StreamBoundary();
        $stream->setOwner($owner);
        $stream->setName($name);
        $stream->setPublic($public);

        return $stream;
    }
}