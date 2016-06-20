<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity()
 * @ORM\Table(name="events")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({  "add" = "AddBoundaryEvent",
 *                          "change" = "ChangeLayerValueEvent",
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
     * @var
     */
    private $events;


    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->events = new ArrayCollection();
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
}
