<?php

namespace AppBundle\Entity;

use AppBundle\Model\PropertyType;
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
     * @ORM\Column(name="property_type", type="property_type", nullable=true)
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
