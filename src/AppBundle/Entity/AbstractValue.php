<?php

namespace AppBundle\Entity;

use AppBundle\Model\PropertyValueInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * AbstractValue
 * 
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity()
 * @ORM\Table(name="values")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({  "value" = "PropertyValue",
 *                          "timevalue" = "PropertyTimeValue",
 *                          "fixedintervalvalue" = "PropertyFixedIntervalValue"
 * })
 */

abstract class AbstractValue implements PropertyValueInterface
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true)
     * @JMS\Type("string")
     * @JMS\Groups("modeldetails")
     */
    private $id;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
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
}
