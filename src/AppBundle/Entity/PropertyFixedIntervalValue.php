<?php

namespace AppBundle\Entity;

use AppBundle\Model\TimeValueFactory;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @ORM\Table(name="property_fixed_interval_values")
 */
class PropertyFixedIntervalValue extends AbstractValue
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_time", type="datetimetz")
     * @JMS\Groups("modeldetails")
     */
    private $dateTimeBegin;

    /**
     * @var String
     * more info herer: http://php.net/manual/de/dateinterval.construct.php
     *
     * @ORM\Column(name="interval", type="string", length=255)
     * @JMS\Groups("modeldetails")
     */
    private $dateTimeIntervalString;

    /**
     * @var array
     *
     * @ORM\Column(name="values", type="simple_array")
     * @JMS\Groups({"modeldetails", "modelobjectdetails"})
     */
    private $values;

    /**
     * Set dateTimeBegin
     *
     * @param \DateTime $dateTimeBegin
     * @return PropertyFixedIntervalValue
     */
    public function setDateTimeBegin($dateTimeBegin)
    {
        $this->dateTimeBegin = $dateTimeBegin;

        return $this;
    }

    /**
     * Get dateTimeBegin
     *
     * @return \DateTime 
     */
    public function getDateTimeBegin()
    {
        return $this->dateTimeBegin;
    }

    /**
     * Set dateTimeInterval
     *
     * @param string $dateTimeIntervalString
     * @return PropertyFixedIntervalValue
     */
    public function setDateTimeIntervalString($dateTimeIntervalString)
    {
        $this->dateTimeIntervalString = $dateTimeIntervalString;

        return $this;
    }

    /**
     * Get dateTimeInterval
     *
     * @return \DateInterval
     */
    public function getDateTimeIntervalString()
    {
        return $this->dateTimeIntervalString;
    }

    /**
     * Set values
     *
     * @param array $values
     * @return PropertyFixedIntervalValue
     */
    public function setValues($values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Get values
     *
     * @return array 
     */
    public function getValues()
    {
        return $this->values;
    }

    public function getDateBegin()
    {
        return $this->dateTimeBegin;
    }

    public function getDateEnd()
    {
        if ($this->dateTimeIntervalString)
        {
            $interval = new \DateInterval($this->dateTimeIntervalString);
            $dateEnd = clone $this->getDateBegin();
            for ($i = 0; $i < $this->getNumberOfValues(); $i++)
            {
                $dateEnd->add($interval);
            }
            return $dateEnd;

        } else
        {
            return null;
        }
    }

    public function getNumberOfValues()
    {
        if ($this->values)
        {
            return count($this->values);
        } else
        {
            return 0;
        }
    }

    public function getTimeValues()
    {
        $timeValues = array();
        $dateTime = clone $this->dateTimeBegin;
        $interval = new \DateInterval($this->dateTimeIntervalString);

        for ($i = 0; $i < count($this->values); $i++)
        {
            if ($i != 0)
            {
                $dateTime = clone $dateTime;
                $dateTime->add($interval);
            }
            $timeValues[] = TimeValueFactory::create()
            ->setDatetime($dateTime)
            ->setValue($this->values[$i]);
        }

        return $timeValues;
    }
}
