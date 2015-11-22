<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * AbstractValue
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity()
 * @ORM\Table(name="values")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({  "value" = "PropertyValue",
 *                          "timevalue" = "PropertyTimeValue",
 *                          "fixedintervalvalue" = "PropertyFixedIntervalValue"
 * })
 * @JMS\ExclusionPolicy("all")
 */

class AbstractValue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var ArrayCollection Property
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Property", inversedBy="values")
     * @ORM\JoinColumn(name="property", referencedColumnName="id")
     */
    private $property;

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
     * Set property
     *
     * @param \AppBundle\Entity\Property $property
     * @return AbstractValue
     */
    public function setProperty(Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return \AppBundle\Entity\Property 
     */
    public function getProperty()
    {
        return $this->property;
    }
}
