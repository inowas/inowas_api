<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TimeSeries
 *
 * @ORM\Table(name="inowas_raster")
 * @ORM\Entity
 */
class Raster
{
    /**
     * @var integer
     *
     * @ORM\Column(name="rid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $rid;

    /**
     * @var float
     *
     * @ORM\Column(name="rast", type="string")
     */
    private $rast;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getRid()
    {
        return $this->rid;
    }

    /**
     * Set rast
     *
     * @param string $rast
     * @return Raster
     */
    public function setRast($rast)
    {
        $this->rast = $rast;

        return $this;
    }

    /**
     * Get rast
     *
     * @return string 
     */
    public function getRast()
    {
        return $this->rast;
    }
}
