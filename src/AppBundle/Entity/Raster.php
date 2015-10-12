<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Raster
 *
 * @ORM\Table(name="inowas_raster")
 * @ORM\Entity
 */
class Raster
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="rast", type="text")
     */
    private $rast;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
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
