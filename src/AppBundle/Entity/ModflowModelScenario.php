<?php

namespace AppBundle\Entity;

use AppBundle\Entity\AbstractEvent as Event;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Inowas\PyprocessingBundle\Model\Modflow\ModflowScenarioInterface;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationProperties;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * ModelScenario
 *
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="model_scenarios")
 * @ORM\Entity()
 */
class ModflowModelScenario implements ModflowScenarioInterface
{
    /**
     * @var Uuid
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true)
     * @JMS\Type("string")
     * @JMS\Groups({"scenariodetails"})
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean")
     * @JMS\Groups({"scenariodetails"})
     */
    protected $public;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255)
     * @JMS\Groups({"scenariodetails"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @JMS\Groups({"scenariodetails"})
     */
    private $description;

    /**
     * Heads-array with key, value = totim => flopy3dArray
     * @var array
     *
     * @ORM\Column(name="heads", type="json_array", nullable=true)
     */
    private $heads;

    /**
     * @var ModFlowModel
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ModFlowModel", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="model_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $baseModel;

    /**
     * @var ArrayCollection $events
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\AbstractEvent", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="scenarios_events",
     *     joinColumns={@ORM\JoinColumn(name="scenario_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE")}
     *     )
     */
    private $events;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime")
     * @JMS\Groups({"scenariodetails"})
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modified", type="datetime")
     * @JMS\Groups({"scenariodetails"})
     */
    private $dateModified;

    /**
     * @var integer
     *
     * @ORM\Column(name="`order`", type="integer", nullable=true)
     * @JMS\Groups({"scenariodetails"})
     */
    private $order;

    /**
     * ModelScenario constructor.
     * @param ModFlowModel $model
     */
    public function __construct(ModFlowModel $model)
    {
        $this->id = Uuid::uuid4();
        $this->events = new ArrayCollection();
        $this->baseModel = $model;
        $this->dateCreated = new \DateTime();
        $this->dateModified = new \DateTime();
        $this->public = true;
        $this->order = $model->getScenarios()->count();
        $model->registerScenario($this);
    }

    /**
     * Get id
     *
     * @return UuidInterface
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ModflowModelScenario
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return ModflowModelScenario
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeads()
    {
        return $this->heads;
    }

    /**
     * @param array $heads
     * @return ModflowModelScenario
     */
    public function setHeads(array $heads)
    {
        $this->heads = $heads;
        return $this;
    }

    /**
     * Get model
     *
     * @return \AppBundle\Entity\ModFlowModel
     */
    public function getBaseModel()
    {
        return $this->baseModel;
    }

    /**
     * @param Event $event
     * @return $this
     */
    public function addEvent(Event $event){
        if (!$this->events->contains($event)){
            $this->events[] = $event;
        };

        return $this;
    }

    /**
     * @param Event $event
     * @return $this
     */
    public function removeEvent(Event $event){
        if ($this->events->contains($event)){
            $this->events->removeElement($event);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEvents(){
        return $this->events;
    }

    /**
     * @return \AppBundle\Entity\ModFlowModel
     */
    public function getModel(){
        if ($this->events instanceof Collection){
            $this->applyAddEvents($this->baseModel, $this->events);
            $this->applyChangeEvents($this->baseModel, $this->events);
        }

        return $this->baseModel;
    }

    /**
     * @param ModFlowModel $model
     * @param Collection $events
     */
    private function applyAddEvents(ModFlowModel $model, Collection $events){
        foreach ($events as $event) {
            if ($event instanceof AddEvent){
                $event->applyTo($model);
            }
        }
    }

    /**
     * @param ModFlowModel $model
     * @param Collection $events
     */
    private function applyChangeEvents(ModFlowModel $model, Collection $events)
    {
        foreach ($events as $event) {
            if ($event instanceof ChangeEvent){
                $event->applyTo($model);
            }
        }
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @return \DateTime
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * @ORM\PrePersist()
     */
    public function updateDateModified()
    {
        $this->dateModified = new \DateTime();
    }

    /**
     * @return bool
     */
    public function isModelScenario(){
        return true;
    }

    /**
     * @param BoundaryModelObject $boundary
     * @return mixed
     */
    public function addBoundary(BoundaryModelObject $boundary)
    {
        $this->addEvent(new AddBoundaryEvent($boundary));
        return $this;
    }

    /**
     * @return array
     */
    public function getBoundaries(){
        $boundaries = [];
        foreach ($this->events as $event){
            if ($event instanceof AddBoundaryEvent){
                $boundaries[] = $event->getBoundary();
            }
        }

        return $boundaries;
    }

    /**
     * @param BoundaryModelObject $origin
     * @param BoundaryModelObject $newBoundary
     * @return mixed
     */
    public function changeBoundary(BoundaryModelObject $origin, BoundaryModelObject $newBoundary)
    {
        $this->addEvent(new ChangeBoundaryEvent($origin, $newBoundary));
        return $this;
    }

    /**
     * @param BoundaryModelObject $boundary
     * @return mixed
     */
    public function removeBoundary(BoundaryModelObject $boundary)
    {
        $this->addEvent(new RemoveBoundaryEvent($boundary));
        return $this;
    }

    /**
     * @param FlopyCalculationProperties $calculationProperties
     * @return mixed
     */
    public function addCalculationProperties(FlopyCalculationProperties $calculationProperties)
    {
        $this->addEvent(new AddCalculationPropertiesEvent($calculationProperties));
        return $this;
    }

    /**
     * @return bool
     */
    public function isScenario()
    {
        return true;
    }

    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     * @return ModflowModelScenario
     */
    public function setOwner(User $owner): ModflowModelScenario
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isPublic(): bool
    {
        return $this->public;
    }

    /**
     * @param boolean $public
     * @return ModflowModelScenario
     */
    public function setPublic(bool $public): ModflowModelScenario
    {
        $this->public = $public;
        return $this;
    }

    /**
     * @return integer
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param string $order
     * @return ModflowModelScenario
     */
    public function setOrder(string $order): ModflowModelScenario
    {
        $this->order = $order;
        return $this;
    }
}
