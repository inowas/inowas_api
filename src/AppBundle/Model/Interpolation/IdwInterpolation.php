<?php

namespace AppBundle\Model\Interpolation;

use JMS\Serializer\Annotation as JMS;

class IdwInterpolation extends AbstractInterpolation
{
    /** @JMS\Groups({"interpolation"}) */
    protected $type = 'idw';
}