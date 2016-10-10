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
     * @JMS\Groups({"modeldetails", "modelobjectdetails", "soilmodellayers"})
     */
    private $value;

    /**
     * @var Raster $raster
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Raster", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="raster_id", referencedColumnName="id", onDelete="SET NULL")
     * @JMS\Groups({"modeldetails", "modelobjectdetails", "soilmodellayers"})
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

    /**
     * @return bool
     */
    public function hasValue(){
       if (null === $this->value){
           return false;
       }
        return true;
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
            TimeValueFactory::create()
                ->setDatetime($this->getDateBegin())
                ->setValue($this->value)
                ->setRaster($this->raster)
        );
    }

    /**
     * Set raster
     *
     * @param Raster $raster
     *
     * @return PropertyValue
     */
    public function setRaster(Raster $raster = null)
    {
        $this->raster = $raster;

        return $this;
    }

    /**
     * Get raster
     *
     * @return Raster
     */
    public function getRaster()
    {
        return $this->raster;
    }

    /**
     * @return bool
     */
    public function hasRaster(){
        if (!$this->raster instanceof Raster){
            return false;
        }
        return true;
    }
}
