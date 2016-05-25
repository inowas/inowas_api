<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * ModFlowKernel
 * 
 * @ORM\Entity()
 * @JMS\ExclusionPolicy("all")
 */
abstract class ModFlowKernel extends AbstractKernel
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true)
     * @JMS\Type("string")
     */
    private $id;

    public function __construct()
    {
        parent::__construct();
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
