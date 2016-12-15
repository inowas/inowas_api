<?php

namespace Inowas\ScenarioAnalysisBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;

class Scenario
{

    /**
     * @var Uuid
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var Uuid
     */
    protected $baseModelId;

    /**
     * @var ArrayCollection
     */
    protected $events;

    /**
     * @var integer
     */
    protected $order;

    /**
     * Scenario constructor.
     * @param Uuid $modelId
     */
    public function __construct(Uuid $modelId)
    {
        $this->id = Uuid::uuid4();
        $this->events = new ArrayCollection();
        $this->baseModelId = $modelId;
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
     * @return Scenario
     */
    public function setName(string $name): Scenario
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Scenario
     */
    public function setDescription(string $description): Scenario
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Uuid
     */
    public function getBaseModelId(): Uuid
    {
        return $this->baseModelId;
    }

    /**
     * @param Uuid $baseModelId
     * @return Scenario
     */
    public function setBaseModelId(Uuid $baseModelId): Scenario
    {
        $this->baseModelId = $baseModelId;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEvents(): ArrayCollection
    {
        return $this->events;
    }

    /**
     * @param ArrayCollection $events
     * @return Scenario
     */
    public function setEvents(ArrayCollection $events): Scenario
    {
        $this->events = $events;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return Scenario
     */
    public function setOrder(int $order): Scenario
    {
        $this->order = $order;
        return $this;
    }
}