<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity()
 * @ORM\Table(name="events")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({  "add_bound" = "AddBoundaryEvent",
 *                          "add_calc" = "AddCalculationPropertiesEvent",
 *                          "change_bound" = "ChangeBoundaryEvent",
 *                          "change_layer" = "ChangeLayerValueEvent",
 *                          "remove" = "RemoveEvent"
 * })
 */
abstract class AbstractEvent
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true)
     * @JMS\Type("string")
     */
    private $id;


    /**
     * @var bool
     *
     * @ORM\Column(name="mutable", type="boolean")
     */
    private $mutable = true;


    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->mutable = true;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function isMutable(): bool
    {
        return $this->mutable;
    }

    /**
     * @param boolean $mutable
     * @return AbstractEvent
     */
    public function setMutable(bool $mutable): AbstractEvent
    {
        $this->mutable = $mutable;
        return $this;
    }
}
