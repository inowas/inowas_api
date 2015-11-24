<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * FeatureProperty
 *
 * @ORM\Table(name="properties")
 * @ORM\Entity
 */
class Property
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var ModelObject
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ModelObject", inversedBy="properties")
     * @ORM\JoinColumn(name="model_object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $modelObject;

    /**
     * @var PropertyType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PropertyType")
     * @ORM\JoinColumn(name="property_type_id", referencedColumnName="id")
     */
    private $propertyType;

    /**
     * @var ArrayCollection Values
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AbstractValue", mappedBy="property")
     */
    private $values;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->values = new ArrayCollection();
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
     * Set type
     *
     * @param \AppBundle\Entity\PropertyType $propertyType
     * @return Property
     */
    public function setType(PropertyType $propertyType = null)
    {
        $this->propertyType = $propertyType;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\PropertyType
     */
    public function getType()
    {
        return $this->propertyType;
    }

    /**
     * Set modelObject
     *
     * @param \AppBundle\Entity\ModelObject $modelObject
     * @return Property
     */
    public function setModelObject(ModelObject $modelObject = null)
    {
        $this->modelObject = $modelObject;

        return $this;
    }

    /**
     * Get modelObject
     *
     * @return \AppBundle\Entity\ModelObject 
     */
    public function getModelObject()
    {
        return $this->modelObject;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Property
     */
    public function setName($name=null)
    {
        if (is_null($name))
        {
            $name = "";
        }
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
     * Add values
     *
     * @param \AppBundle\Entity\AbstractValue $values
     * @return Property
     */
    public function addValue(\AppBundle\Entity\AbstractValue $values)
    {
        $this->values[] = $values;

        return $this;
    }

    /**
     * Remove values
     *
     * @param \AppBundle\Entity\AbstractValue $values
     */
    public function removeValue(\AppBundle\Entity\AbstractValue $values)
    {
        $this->values->removeElement($values);
    }

    /**
     * Get values
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getValues()
    {
        return $this->values;
    }
}
