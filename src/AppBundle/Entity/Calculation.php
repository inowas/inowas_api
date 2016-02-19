<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Calculation
 *
 * @ORM\Table(name="calculation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CalculationRepository")
 */
class Calculation
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
     * @var Project
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Project", mappedBy="calculation")
     */
    private $project;

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
     * @var
     *
     * @ORM\Column(name="values", type="simple_raster", nullable=true)
     */
    private $values;

    /**
     * @var
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Raster", cascade={"persist", "remove"})
     */
    private $raster;

    /**
     * Calculation constructor.
     */
    public function __construct()
    {
        $this->raster = new Raster();
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
     * Set project
     *
     * @param \AppBundle\Entity\Project $project
     *
     * @return Calculation
     */
    public function setProject(\AppBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \AppBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set result
     *
     * @param \AppBundle\Entity\ModelObject $result
     *
     * @return Calculation
     */
    public function setResult(\AppBundle\Entity\ModelObject $result = null)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return \AppBundle\Entity\ModelObject
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set values
     *
     * @param array $values
     *
     * @return Calculation
     */
    public function setValues($values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Set raster
     *
     * @param raster $raster
     *
     * @return Calculation
     */
    public function setRaster($raster)
    {
        $this->raster = $raster;

        return $this;
    }

    /**
     * Get raster
     *
     * @return raster
     */
    public function getRaster()
    {
        return $this->raster;
    }
}
