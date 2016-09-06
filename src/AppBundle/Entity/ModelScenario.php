<?php

namespace AppBundle\Entity;

use AppBundle\Entity\AbstractEvent as Event;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
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
class ModelScenario
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
     */
    protected $public;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255)
     */
    private $name;

    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     * @return ModelScenario
     */
    public function setOwner(User $owner): ModelScenario
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
     * @return ModelScenario
     */
    public function setPublic(bool $public): ModelScenario
    {
        $this->public = $public;
        return $this;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="image_file", type="string", length=255, nullable=true)
     */
    private $imageFile;

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
     * @JMS\Type("ArrayCollection<AppBundle\Entity\AbstractModel>")
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
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modified", type="datetime")
     * @JMS\Groups({"list", "details"})
     */
    private $dateModified;

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
     * @return ModelScenario
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
     * @return ModelScenario
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param string $imageFile
     * @return $this
     */
    public function setImageFile($imageFile)
    {
        $this->imageFile = $imageFile;
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
     * @return ModelScenario
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
        foreach ($this->events as $event) {
            $this->applyAddEvents($this->baseModel, $event);
        }

        foreach ($this->events as $event) {
            $this->applyChangeEvents($this->baseModel, $event);
        }

        return $this->baseModel;
    }

    /**
 * @param ModFlowModel $model
 * @param AbstractEvent $event
 */
    private function applyAddEvents(ModFlowModel $model, Event $event){
        if ($event instanceof AddBoundaryEvent) {
            if ($event->getBoundary() instanceof BoundaryModelObject){
                $model->addBoundary($event->getBoundary()->setMutable(true));
            }
        }
    }

    /**
     * @param ModFlowModel $model
     * @param AbstractEvent $event
     */
    private function applyChangeEvents(ModFlowModel $model, Event $event)
    {
        if ($event instanceof ChangeBoundaryEvent) {
            if ($event->getOrigin() instanceof BoundaryModelObject && $event->getNewBoundary() instanceof BoundaryModelObject){

                /** @var BoundaryModelObject $boundary */
                foreach ($model->getBoundaries()->toArray() as $bKey => $boundary){
                    if ($boundary->getId() == $event->getOrigin()->getId()){
                        $model->removeBoundary($boundary);
                        $model->addBoundary($event->getNewBoundary());
                    }
                }
            }
        }

        if ($event instanceof ChangeLayerValueEvent) {
            $layer = $event->getLayer();
            /** @var GeologicalLayer $geologicalLayer */
            foreach ($model->getSoilModel()->getGeologicalLayers() as $geologicalLayer) {
                if ($geologicalLayer->getId() == $layer->getId()) {
                    $geologicalLayer->addValue($event->getPropertyType(), $event->getValue());
                }
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
}
