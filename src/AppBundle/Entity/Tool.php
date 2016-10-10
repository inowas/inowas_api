<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * Tool
 *
 * @ORM\Table(name="tools")
 * @ORM\Entity()
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
     * @param AbstractModel $model
     * @return Tool
     */
    public function setModel(AbstractModel $model = null)
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
     * @param AbstractKernel $kernel
     *
     * @return Tool
     */
    public function setKernel(AbstractKernel $kernel = null)
    {
        $this->kernel = $kernel;

        return $this;
    }

    /**
     * Get kernel
     *
     * @return AbstractKernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }
}
