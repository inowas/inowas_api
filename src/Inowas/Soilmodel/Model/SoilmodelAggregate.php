<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;

use Inowas\Common\Id\UserId;

use Inowas\Soilmodel\Model\Event\SoilmodelBoreLogWasAdded;
use Inowas\Soilmodel\Model\Event\SoilmodelBoreLogWasRemoved;
use Inowas\Soilmodel\Model\Event\SoilmodelDescriptionWasChanged;
use Inowas\Soilmodel\Model\Event\SoilmodelGeologicalLayerWasAdded;
use Inowas\Soilmodel\Model\Event\SoilmodelGeologicalLayerWasRemoved;
use Inowas\Soilmodel\Model\Event\SoilmodelNameWasChanged;
use Inowas\Soilmodel\Model\Event\SoilmodelWasCreated;
use Inowas\Soilmodel\Model\Event\SoilmodelWasDeleted;
use Prooph\EventSourcing\AggregateRoot;

class SoilmodelAggregate extends AggregateRoot
{

    /** @var  SoilmodelId */
    protected $soilmodelId;

    /** @var  UserId */
    protected $owner;

    /** @var  bool */
    protected $public;

    /** @var  SoilmodelName */
    protected $name;

    /** @var  SoilmodelDescription */
    protected $description;

    /** @var  array */
    protected $boreLogs;

    /** @var  array */
    protected $layers;

    public static function create(UserId $userId, SoilmodelId $soilmodelId): SoilmodelAggregate
    {
        $self = new self();
        $self->soilmodelId = $soilmodelId;
        $self->owner = $userId;
        $self->public = true;
        $self->name = SoilmodelName::fromString("");
        $self->description = SoilmodelDescription::fromString("");
        $self->boreLogs = [];
        $self->layers = [];

        $self->recordThat(SoilmodelWasCreated::byUserWithId($userId, $soilmodelId));
        return $self;
    }

    public function delete(UserId $userId): void
    {
        $this->recordThat(SoilmodelWasDeleted::byUserWithId($userId, $this->soilmodelId));
    }

    public function changeName(UserId $userId, SoilmodelName $name): void
    {
        $this->name = $name;
        $this->recordThat(SoilmodelNameWasChanged::byUserWithName($userId, $this->soilmodelId, $name));
    }

    public function changeDescription(UserId $userId, SoilmodelDescription $description): void
    {
        $this->description = $description;
        $this->recordThat(SoilmodelDescriptionWasChanged::byUserWithName($userId, $this->soilmodelId, $description));
    }

    public function addBoreLog(UserId $userId, BoreLogId $logId): void
    {
        if (! array_key_exists($logId->toString(), $this->boreLogs)){
            $this->boreLogs[$logId->toString()] = true;
            $this->recordThat(SoilmodelBoreLogWasAdded::byUserWithId($userId, $this->soilmodelId, $logId));
        }
    }

    public function removeBoreLog(UserId $userId, BoreLogId $logId): void
    {
        if (array_key_exists($logId->toString(), $this->boreLogs)){
           unset($this->boreLogs[$logId->toString()]);
            $this->recordThat(SoilmodelBoreLogWasRemoved::byUserWithId($userId, $this->soilmodelId, $logId));
        }

    }

    public function addGeologicalLayer(UserId $userId, GeologicalLayer $layer): void
    {
        $this->layers[$layer->id()->toString()] = $layer;
        $this->recordThat(SoilmodelGeologicalLayerWasAdded::byUserWithId($userId, $this->soilmodelId, $layer));
    }

    public function removeGeologicalLayer(UserId $userId, GeologicalLayer $layer): void
    {
        if (array_key_exists($layer->id()->toString(), $this->layers)){
            unset($this->layers[$layer->id()->toString()]);
            $this->recordThat(SoilmodelGeologicalLayerWasRemoved::byUserWithId($userId, $this->soilmodelId, $layer->id()));
        }
    }

    protected function whenSoilmodelWasCreated(SoilmodelWasCreated $event): void
    {
        $this->soilmodelId = $event->soilmodelId();
        $this->owner = $event->userId();
        $this->boreLogs = [];
        $this->layers = [];
    }

    protected function whenSoilmodelWasDeleted(SoilmodelWasDeleted $event): void
    {}

    protected function whenSoilmodelNameWasChanged(SoilmodelNameWasChanged $event): void
    {
        $this->name = $event->name();
    }

    protected function whenSoilmodelDescriptionWasChanged(SoilmodelDescriptionWasChanged $event): void
    {
        $this->description = $event->description();
    }

    protected function whenSoilmodelBoreLogWasAdded(SoilmodelBoreLogWasAdded $event): void
    {
        $this->boreLogs[$event->boreLogId()->toString()] = true;
    }

    protected function whenSoilmodelBoreLogWasRemoved(SoilmodelBoreLogWasRemoved $event): void
    {
        if (array_key_exists($event->boreLogId()->toString(), $this->boreLogs)){
            unset($this->boreLogs[$event->boreLogId()->toString()]);
        }
    }

    protected function whenSoilmodelGeologicalLayerWasAdded(SoilmodelGeologicalLayerWasAdded $event): void
    {
        $this->layers[$event->layer()->id()->toString()] = $event->layer();
    }

    protected function whenSoilmodelGeologicalLayerWasRemoved(SoilmodelGeologicalLayerWasRemoved $event): void
    {
        if (array_key_exists($event->layerId()->toString(), $this->layers)) {
            unset($this->layers[$event->layerId()->toString()]);
        }
    }

    public function id(): SoilmodelId
    {
        return $this->soilmodelId;
    }

    public function ownerId(): UserId
    {
        return $this->owner;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function name(): SoilmodelName
    {
        return $this->name;
    }

    public function description(): SoilmodelDescription
    {
        return $this->description;
    }

    public function boreLogs(): array
    {
        return $this->boreLogs;
    }

    public function layers(): array
    {
        return $this->layers;
    }

    public function userHasWriteAccess(UserId $userId): bool
    {
        if ($userId->sameValueAs($this->owner)){
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    protected function aggregateId(): string
    {
        return $this->soilmodelId->toString();
    }
}