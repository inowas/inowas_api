<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * AreaType
 *
 * @ORM\Table(name="area_types")
 * @ORM\Entity
 */
class AreaType
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true)
     * @JMS\Type("string")
     * @JMS\Groups({"list", "modelobjectdetails", "soilmodeldetails", "modeldetails"})
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="text")
     * @JMS\Groups({"list", "modelobjectdetails", "soilmodeldetails", "modeldetails"})
     */
    private $name;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
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
     * Set name
     *
     * @param string $name
     * @return AreaType
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
}
