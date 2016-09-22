<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * ModFlowKernel
 *
 * @ORM\Entity()
 * @JMS\ExclusionPolicy("all")
 */
class ModFlowKernel extends AbstractKernel
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ModFlowKernel
     */
    public function setName(string $name): ModFlowKernel
    {
        $this->name = $name;
        return $this;
    }
}
