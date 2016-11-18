<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use Doctrine\Common\Collections\ArrayCollection;

interface BoundaryInterface
{
    public function getType(): string;
    public function getStressPeriods(): ArrayCollection;
}