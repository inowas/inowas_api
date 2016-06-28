<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * Calculation
 *
 * @ORM\Table(name="calculation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CalculationRepository")
 */
class Calculation
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
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = 0;

    /**
     * @var ModelObject
     *
     * @ORM\OneToOne(targetEntity="ModelObject")
     */
    private $result;

    /**
     * Calculation constructor.
     */
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
     * Set status
     *
     * @param integer $status
     *
     * @return Calculation
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Set result
     *
     * @param ModelObject $result
     *
     * @return Calculation
     */
    public function setResult(ModelObject $result = null)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * Get result
     *
     * @return ModelObject
     */
    public function getResult()
    {
        return $this->result;
    }
}
