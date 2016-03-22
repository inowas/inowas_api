<?php

namespace AppBundle\Entity;

use AppBundle\Model\TimeValueFactory;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @ORM\Table(name="property_values")
 */
class PropertyValue extends AbstractValue
{
    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     * @JMS\Groups("modeldetails")
     */
    private $value;

    /**
     * @var Raster $raster
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Raster", cascade={"persist", "remove"})
     * @JMS\Groups("modeldetails")
     */
    private $raster;

    /**
     * Set value
     *
     * @param float $value
     * @return PropertyValue
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float 
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getDateBegin()
    {
        return null;
    }

    public function getDateEnd()
    {
        return null;
    }

    public function getNumberOfValues()
    {
        return 1;
    }

    public function getTimeValues()
    {
        return array(
            TimeValueFactory::setValue($this->value)
        );
    }

    /**
     * Set raster
     *
     * @param \AppBundle\Entity\Raster $raster
     *
     * @return PropertyValue
     */
    public function setRaster(\AppBundle\Entity\Raster $raster = null)
    {
        $this->raster = $raster;

        return $this;
    }

    /**
     * Get raster
     *
     * @return \AppBundle\Entity\Raster
     */
    public function getRaster()
    {
        return $this->raster;
    }
}
