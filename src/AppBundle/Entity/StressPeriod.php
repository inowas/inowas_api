<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * stressPeriod
 *
 * @ORM\Table(name="stress_period")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\stressPeriodRepository")
 */
class StressPeriod
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
     * @var ModFlowModel
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ModFlowModel", inversedBy="stressPeriods")
     * @ORM\JoinColumn(name="model_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $model;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateTimeBegin", type="datetime")
     */
    private $dateTimeBegin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateTimeEnd", type="datetime")
     */
    private $dateTimeEnd;


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
     * Set dateTimeBegin
     *
     * @param \DateTime $dateTimeBegin
     *
     * @return stressPeriod
     */
    public function setDateTimeBegin($dateTimeBegin)
    {
        $this->dateTimeBegin = $dateTimeBegin;

        return $this;
    }

    /**
     * Get dateTimeBegin
     *
     * @return \DateTime
     */
    public function getDateTimeBegin()
    {
        return $this->dateTimeBegin;
    }

    /**
     * Set dateTimeEnd
     *
     * @param \DateTime $dateTimeEnd
     *
     * @return stressPeriod
     */
    public function setDateTimeEnd($dateTimeEnd)
    {
        $this->dateTimeEnd = $dateTimeEnd;

        return $this;
    }

    /**
     * Get dateTimeEnd
     *
     * @return \DateTime
     */
    public function getDateTimeEnd()
    {
        return $this->dateTimeEnd;
    }

    /**
     * Set model
     *
     * @param \AppBundle\Entity\ModFlowModel $model
     *
     * @return StressPeriod
     */
    public function setModel(\AppBundle\Entity\ModFlowModel $model = null)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return \AppBundle\Entity\ModFlowModel
     */
    public function getModel()
    {
        return $this->model;
    }
}
