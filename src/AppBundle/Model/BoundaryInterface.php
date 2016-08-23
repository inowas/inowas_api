<?php

namespace AppBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

interface BoundaryInterface
{
    /**
     * @return ArrayCollection
     */
    public function getStressPeriods();

}