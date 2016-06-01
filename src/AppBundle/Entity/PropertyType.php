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
     * @var bool
     *
     * @ORM\Column(name="can_be_static", type="boolean")
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails"})
     */
    private $canBeStatic;

    /**
     * @var bool
     *
     * @ORM\Column(name="can_be_time_dependent", type="boolean")
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails"})
     */
    private $canBeTimeDependent;

    /**
     * PropertyType constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->canBeStatic = false;
        $this->canBeTimeDependent = false;
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
     * @return boolean
     */
    public function isCanBeStatic()
    {
        return $this->canBeStatic;
    }

    /**
     * @param boolean $canBeStatic
     * @return PropertyType
     */
    public function setCanBeStatic($canBeStatic)
    {
        $this->canBeStatic = $canBeStatic;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isCanBeTimeDependent()
    {
        return $this->canBeTimeDependent;
    }

    /**
     * @param boolean $canBeTimeDependent
     * @return PropertyType
     */
    public function setCanBeTimeDependent($canBeTimeDependent)
    {
        $this->canBeTimeDependent = $canBeTimeDependent;
        return $this;
    }
}
