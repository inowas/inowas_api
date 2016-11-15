<?php

namespace Inowas\ModflowBundle\Model;

use Inowas\ModflowBundle\Model\ValueObject\ActiveCells;
use Ramsey\Uuid\Uuid;

abstract class ModelObject
{
    /** @var Uuid */
    protected $id;

    /** @var string */
    protected $name;

    /** @var  ActiveCells */
    protected $activeCells;

    /** @var \DateTime */
    protected $dateCreated;

    /** @var \DateTime */
    protected $dateModified;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->dateCreated = new \DateTime();
        $this->dateModified = new \DateTime();
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ModelObject
     */
    public function setName(string $name): ModelObject
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return ActiveCells
     */
    public function getActiveCells(): ActiveCells
    {
        return $this->activeCells;
    }

    /**
     * @param ActiveCells $activeCells
     * @return ModelObject
     */
    public function setActiveCells(ActiveCells $activeCells): ModelObject
    {
        $this->activeCells = $activeCells;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated(): \DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     * @return ModelObject
     */
    public function setDateCreated(\DateTime $dateCreated): ModelObject
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateModified(): \DateTime
    {
        return $this->dateModified;
    }

    /**
     * @param \DateTime $dateModified
     * @return ModelObject
     */
    public function setDateModified(\DateTime $dateModified): ModelObject
    {
        $this->dateModified = $dateModified;
        return $this;
    }
}
