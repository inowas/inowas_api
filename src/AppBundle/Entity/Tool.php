<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * Tool
 *
 * @ORM\Table(name="tools")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ToolRepository")
 */
class Tool
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
     * @var AbstractModel
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AbstractModel")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\AbstractModel>")
     **/
    private $model;

    /**
     * @var AbstractKernel
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AbstractKernel")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\AbstractKernel>")
     **/
    private $kernel;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
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
     * Set model
     *
     * @param \AppBundle\Entity\AbstractModel $model
     *
     * @return Tool
     */
    public function setModel(\AppBundle\Entity\AbstractModel $model = null)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return \AppBundle\Entity\AbstractModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set kernel
     *
     * @param \AppBundle\Entity\AbstractKernel $kernel
     *
     * @return Tool
     */
    public function setKernel(\AppBundle\Entity\AbstractKernel $kernel = null)
    {
        $this->kernel = $kernel;

        return $this;
    }

    /**
     * Get kernel
     *
     * @return \AppBundle\Entity\AbstractKernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }
}
