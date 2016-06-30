<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ChangeLayerValueEvent extends AbstractEvent
{
    /**
     * @var ModelObject
     *
     * @ORM\ManyToOne(targetEntity="ModelObject")
     */
    private $origin;

    /** 
     * @var AbstractValue $value
     *
     * @ORM\ManyToOne(targetEntity="AbstractValue", cascade={"persist", "remove"})
     */
    private $value;

    /**
     * @var PropertyType $value
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PropertyType")
     */
    private $propertyType;

    public function __construct(GeologicalLayer $origin, PropertyType $propertyType, AbstractValue $value)
    {
        parent::__construct();
        $this->origin = $origin;
        $this->propertyType = $propertyType;
        $this->value = $value;
    }

    /**
     * @return ModelObject
     */
    public function getLayer()
    {
        return $this->origin;
    }

    /**
     * @return PropertyType
     */
    public function getPropertyType()
    {
        return $this->propertyType;
    }

    /**
     * @return AbstractValue
     */
    public function getValue()
    {
        return $this->value;
    }
}
