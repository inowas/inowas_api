<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GeologicalUnitRepository")
 * @ORM\Table(name="geological_units")
 */
class GeologicalUnit extends ModelObject
{
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    protected $type = 'geologicalunit';

    /**
     * @var $elevation
     *
     * @ORM\Column(name="top_elevation", type="float", nullable=true)
     * @JMS\Groups({"details", "modelobjectdetails"})
     */
    private $topElevation;

    /**
     * @var $elevation
     *
     * @ORM\Column(name="bottom_elevation", type="float", nullable=true)
     * @JMS\Groups({"details", "modelobjectdetails"})
     */
    private $bottomElevation;

    /**
     * Set topElevation
     *
     * @param float $topElevation
     * @return GeologicalUnit
     */
    public function setTopElevation($topElevation)
    {
        $this->topElevation = $topElevation;

        return $this;
    }

    /**
     * Get topElevation
     *
     * @return float 
     */
    public function getTopElevation()
    {
        return $this->topElevation;
    }

    /**
     * Set bottomElevation
     *
     * @param float $bottomElevation
     * @return GeologicalUnit
     */
    public function setBottomElevation($bottomElevation)
    {
        $this->bottomElevation = $bottomElevation;

        return $this;
    }

    /**
     * Get bottomElevation
     *
     * @return float 
     */
    public function getBottomElevation()
    {
        return $this->bottomElevation;
    }
}
