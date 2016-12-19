<?php

namespace Inowas\ScenarioAnalysisBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\ModflowModelInterface;
use Ramsey\Uuid\Uuid;

class Scenario implements ModflowModelInterface, \JsonSerializable
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
     * @param ModflowModel $model
     */
    public function __construct(ModflowModel $model)
    {
        $this->order = 0;
        $this->id = Uuid::uuid4();
        $this->events = new ArrayCollection();
        $this->baseModelId = $model->getId();
    }

    public function __clone()
    {
        $this->id = Uuid::uuid4();
        $this->order = $this->order+1;
        $this->events = new ArrayCollection();
        $this->name = sprintf('%s (copy)', $this->getName());
        $this->description = sprintf('Copy of %s', $this->getDescription());
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
        if (is_null($this->name))
        {
            $this->name = "";
        }
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
        if (is_null($this->description))
        {
            $this->description = "";
        }
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
     * @return Collection
     */
    public function getEvents(): Collection
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
     * @param Event $event
     * @return $this
     */
    public function addEvent(Event $event)
    {
        $this->events[] = $event;
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

    /**
     * @param ModflowModel $modflowModel
     * @return ModflowModel
     */
    public function applyTo(ModflowModel $modflowModel): ModflowModel
    {
        foreach ($this->events as $event){
            $event->applyTo($modflowModel);
        }

        return $modflowModel;
    }

    /**
     * @return object
     */
    function jsonSerialize()
    {
        return (object) array(
            'id' => $this->getId()->toString(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'order' => $this->getOrder()
        );
    }
}
