<?php

namespace Inowas\ModflowBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

abstract class Boundary extends ModelObject
{
    abstract public function addStressPeriod(StressPeriodInterface $stressPeriod);

    abstract public function getStressPeriods(): ArrayCollection;
}