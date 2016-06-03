<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * FeatureProperty
 *
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="properties")
 * @ORM\Entity()
 * @JMS\AccessorOrder("custom", custom = {"id", "name", "description", "propertyType", "date_time_begin", "date_time_end"})
 */
class Property
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true)
     * @JMS\Type("string")
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\AbstractValue", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="properties_values",
     *     joinColumns={@ORM\JoinColumn(name="property_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="value_id", referencedColumnName="id", onDelete="CASCADE")}
     *     )
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
        $this->id = Uuid::uuid4();
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
     * @param \AppBundle\Entity\AbstractValue $newValue
     * @return Property
     */
    public function addValue(AbstractValue $newValue)
    {
        if (!$this->values->contains($newValue))
        {
            if ($newValue instanceof PropertyValue){
                $this->values->clear();
                $this->values[] = $newValue;
                return $this;
            }

            if ($newValue instanceof PropertyTimeValue){
                /** @var AbstractValue $value */
                foreach ($this->values as $key => $value) {
                    if ($value->getDateBegin() == $newValue->getDateBegin()) {
                        $this->values[$key] = $newValue;
                        return $this;
                    }
                }
                $this->values[] = $newValue;
            }

            if ($newValue instanceof PropertyFixedIntervalValue){
                /** @var AbstractValue $value */
                foreach ($this->values as $key => $value) {
                    if ($value->getDateBegin() == $newValue->getDateBegin()) {
                        $this->values[$key] = $newValue;
                        return $this;
                    }
                }
                $this->values[] = $newValue;
            }
        }

        return $this;
    }

    /**
     * Remove value
     *
     * @param \AppBundle\Entity\AbstractValue $value
     */
    public function removeValue(AbstractValue $value)
    {
        if ($this->values->contains($value))
        {
            $this->values->removeElement($value);
        }
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
