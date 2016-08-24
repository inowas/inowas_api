<?php

namespace AppBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

interface BoundaryInterface
{
    /**
     * @return ArrayCollection
     */
    public function getStressPeriods();

    /**
     * @param array $stressPeriodData
     * @param ArrayCollection $globalStressPeriods
     * @return mixed
     */
    public function addStressPeriodData(array $stressPeriodData, ArrayCollection $globalStressPeriods);
}