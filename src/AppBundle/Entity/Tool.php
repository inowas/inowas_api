<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Tool
 *
 * @ORM\Table(name="tools")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ToolRepository")
 */
class Tool
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ArrayCollection Tool $tools
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Application", inversedBy="tools")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Application>")
     **/
    private $application;

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
     * Set application
     *
     * @param \AppBundle\Entity\Application $application
     *
     * @return Tool
     */
    public function setApplication(\AppBundle\Entity\Application $application = null)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get application
     *
     * @return \AppBundle\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
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
