<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * Application
 *
 * @ORM\Table(name="application")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ApplicationRepository")
 */
class Application
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true)
     * @JMS\Type("string")
     * @JMS\Groups({"applicationList", "applicationDetails"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255, nullable=true)
     * @JMS\Groups({"applicationList", "applicationDetails"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @JMS\Groups({"applicationList", "applicationDetails"})
     */
    private $description;

    /**
     * @var ArrayCollection Tool
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tool")
     * @ORM\JoinTable(name="applications_tools",
     *     joinColumns={@ORM\JoinColumn(name="application_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tool_id", referencedColumnName="id")}
     * )
     *
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Tool>")
     * @JMS\Groups({"applicationDetails"})
     **/
    private $tools;

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
     * Constructor
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->tools = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Application
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

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Application
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add tool
     *
     * @param Tool $tool
     *
     * @return Application
     */
    public function addTool(Tool $tool)
    {
        if (! $this->tools->contains($tool)) {
            $this->tools[] = $tool;
        };

        return $this;
    }

    /**
     * Remove tool
     *
     * @param Tool $tool
     */
    public function removeTool(Tool $tool)
    {
        $this->tools->removeElement($tool);
    }

    /**
     * Get tools
     *
     * @return ArrayCollection
     */
    public function getTools()
    {
        return $this->tools;
    }
}
