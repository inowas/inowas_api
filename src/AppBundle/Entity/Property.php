<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * FeatureProperty
 *
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="properties")
 * @ORM\Entity()
 * @JMS\AccessorOrder("alphabetical")
 */
class Property
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails", "soilmodeldetails"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails", "soilmodeldetails"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @JMS\Groups({"projectList", "projectDetails", "modelobjectdetails", "soilmodeldetails"})
     */
    private $description;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PropertyType", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="property_type_id", referencedColumnName="id", onDelete="SET NULL")
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails", "soilmodeldetails"})
     */
    private $propertyType;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AbstractValue", mappedBy="property", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $values;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_time_begin", type="datetime", nullable=true)
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails", "soilmodeldetails"})
     */
    private $dateTimeBegin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_time_end", type="datetime", nullable=true)
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails", "soilmodeldetails"})
     */
    private $dateTimeEnd;

    /**
     * @var integer $numberOfValues
     *
     * @ORM\Column(name="number_of_values", type="integer")
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails", "soilmodeldetails"})
     */
    private $numberOfValues;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->values = new ArrayCollection();
    }

    /**
     * Set id
     * 
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
    public function setPropertyType(PropertyType $propertyType = null)
    {
        $this->propertyType = $propertyType;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\PropertyType
     */
    public function getPropertyType()
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
     * @param \AppBundle\Entity\AbstractValue $value
     * @return Property
     */
    public function addValue(\AppBundle\Entity\AbstractValue $value)
    {
        $value->setProperty($this);
        $this->values[] = $value;
        return $this;
    }

    /**
     * Remove value
     *
     * @param \AppBundle\Entity\AbstractValue $value
     */
    public function removeValue(\AppBundle\Entity\AbstractValue $value)
    {
        $value->setProperty(null);
        $this->values->removeElement($value);
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

    /**
     * @return \DateTime
     */
    public function getDateTimeBegin()
    {
        return $this->dateTimeBegin;
    }

    /**
     * @param \DateTime $dateTimeBegin
     */
    public function setDateTimeBegin($dateTimeBegin)
    {
        $this->dateTimeBegin = $dateTimeBegin;
    }

    /**
     * @return \DateTime
     */
    public function getDateTimeEnd()
    {
        return $this->dateTimeEnd;
    }

    /**
     * @param \DateTime $dateTimeEnd
     */
    public function setDateTimeEnd($dateTimeEnd)
    {
        $this->dateTimeEnd = $dateTimeEnd;
    }

    /**
     * @return int
     */
    public function getNumberOfValues()
    {
        return $this->numberOfValues;
    }

    /**
     * @param int $numberOfValues
     */
    public function setNumberOfValues($numberOfValues)
    {
        $this->numberOfValues = $numberOfValues;
    }

    /**
     * @ORM\PreFlush()
     */
    public function setDatesAndNumberOfValues()
    {
        if ($this->values)
        {
            $numberOfValues = 0;
            $dateTimeBegin = null;
            $dateTimeEnd = null;

            /** @var AbstractValue $value */
            foreach ($this->values as $value)
            {
                $numberOfValues += $value->getNumberOfValues();

                if ($dateTimeBegin == null || $dateTimeBegin > $value->getDateBegin())
                {
                    if ($value->getDateBegin()) {
                        $dateTimeBegin = $value->getDateBegin();
                    }
                }

                if ($dateTimeEnd == null || $dateTimeEnd < $value->getDateEnd())
                {
                    if ($value->getDateEnd())
                    {
                        $dateTimeEnd = $value->getDateEnd();
                    }
                }
            }

            $this->numberOfValues = $numberOfValues;
            $this->dateTimeBegin = $dateTimeBegin;
            $this->dateTimeEnd = $dateTimeEnd;
        }
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("values")
     * @JMS\Groups({"modeldetails", "modelobjectdetails"})
     */
    public function getTimeValues()
    {
        $timeValues = array();

        /** @var AbstractValue $value */
        foreach ($this->values as $value)
        {
            $timeValues = array_merge($timeValues, $value->getTimeValues());
        }

        return $timeValues;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Property
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
