<?php

namespace AppBundle\Model\Interpolation;

use JMS\Serializer\Annotation as JMS;

class KrigingInterpolation extends AbstractInterpolation
{
    /** @JMS\Groups({"interpolation"}) */
    protected $type = 'kriging';
}