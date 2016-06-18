<?php

namespace AppBundle\Entity;

use AppBundle\Model\Point;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

class IndustrialWell extends Well
{
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    protected $wellType = 'iw';
}
