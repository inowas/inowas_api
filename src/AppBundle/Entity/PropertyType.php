<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * ModelObjectPropertyType
 *
 * @ORM\Table(name="property_types")
 * @ORM\Entity
 */
class PropertyType
{
    const STATIC_VALUE_ONLY = 1;
    const TIME_DEPENDENT_VALUE_ONLY = 2;
    const STATIC_AND_TIME_DEPENDENT_VALUES = 3;

    const PROP_TYPE_TOP_ELEVATION = 'et';
    const PROP_TYPE_BOTTOM_ELEVATION = 'eb';

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid")
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="abbreviation", type="string", length=255)
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails"})
     */
    private $abbreviation;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails"})
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="value_type", type="integer")
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails"})
     */
    private $valueType;

    /**
     * PropertyType constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->valueType = self::STATIC_AND_TIME_DEPENDENT_VALUES;
    }

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
     * Set abbreviation
     *
     * @param string $abbreviation
     *
     * @return PropertyType
     */
    public function setAbbreviation($abbreviation)
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    /**
     * Get abbreviation
     *
     * @return string
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return PropertyType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getValueType()
    {
        return $this->valueType;
    }

    /**
     * @param int $valueType
     * @return PropertyType
     */
    public function setValueType($valueType)
    {
        $this->valueType = $valueType;
        return $this;
    }
}
