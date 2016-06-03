<?php

namespace AppBundle\Model\Interpolation;

use JMS\Serializer\Annotation as JMS;

class MeanInterpolation extends AbstractInterpolation
{
    /** @JMS\Groups({"interpolation"}) */
    protected $type = 'mean';
}