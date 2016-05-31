<?php

namespace AppBundle\Model\Interpolation;

use JMS\Serializer\Annotation as JMS;

class GaussianInterpolation extends AbstractInterpolation
{
    /** @JMS\Groups({"interpolation"}) */
    protected $type = 'gaussian';
}