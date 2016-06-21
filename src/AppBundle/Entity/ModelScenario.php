<?php

namespace AppBundle\Entity;

use AppBundle\Entity\AbstractEvent as Event;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

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
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255)
     */
    private $name;

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
     * @var ModFlowModel
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ModFlowModel", cascade={"persist", "remove"})
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
     * @param AbstractModel $model
     */
    public function __construct(AbstractModel $model)
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
     * @return Uuid
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
     * Get model
     *
     * @return \AppBundle\Entity\AbstractModel
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
            $this->events->remove($event);
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
            $this->applyEvent($this->baseModel, $event);
        }

        return $this->baseModel;
    }

    /**
     * @param ModFlowModel $model
     * @param AbstractEvent $event
     */
    private function applyEvent(ModFlowModel $model, Event $event){
        if ($event instanceof AddBoundaryEvent) {
            if ($event->getBoundary() instanceof ModelObject){
                $model->addBoundary($event->getBoundary());
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
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->dateModified = new \DateTime();
    }

}
