<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Event;

use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\SoilmodelDescription;
use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\Common\Soilmodel\SoilmodelName;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class SoilmodelWasCloned extends AggregateChanged
{
    /** @var  SoilmodelId */
    private $soilmodelId;

    /** @var  SoilmodelId */
    private $fromId;

    /** @var  UserId */
    private $userId;

    /** @var  bool */
    private $public;

    /** @var  SoilmodelName */
    private $name;

    /** @var  SoilmodelDescription */
    private $description;

    /** @var  array */
    private $borelogs;

    /** @var  array */
    private $layers;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param SoilmodelId $newId
     * @param SoilmodelId $fromId
     * @param UserId $userId
     * @param bool $public
     * @param SoilmodelName $name
     * @param SoilmodelDescription $description
     * @param array $borelogs
     * @param array $layers
     * @return SoilmodelWasCloned
     */
    public static function byUserWithIds(
        SoilmodelId $newId,
        SoilmodelId $fromId,
        UserId $userId,
        bool $public,
        SoilmodelName $name,
        SoilmodelDescription $description,
        array $borelogs,
        array $layers
    ): SoilmodelWasCloned
    {
        $event = self::occur($newId->toString(),[
            'from_id' => $fromId->toString(),
            'user_id' => $userId->toString(),
            'public' => $public,
            'name' => $name->toString(),
            'description' => $description->toString(),
            'borelogs' => json_encode($borelogs),
            'layers' => json_encode($layers)
        ]);

        $event->fromId = $fromId;
        $event->userId = $userId;
        $event->public = $public;
        $event->name = $name;
        $event->description = $description;
        $event->borelogs = $borelogs;
        $event->layers = $layers;

        return $event;
    }

    public function soilmodelId(): SoilmodelId
    {
        if ($this->soilmodelId === null) {
            $this->soilmodelId = SoilmodelId::fromString($this->aggregateId());
        }

        return $this->soilmodelId;
    }

    public function fromId(): SoilmodelId
    {
        if ($this->fromId === null) {
            $this->fromId = SoilmodelId::fromString($this->payload['from_id']);
        }

        return $this->fromId;
    }

    public function userId(): UserId{
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function isPublic(): bool
    {
        if (null === $this->public) {
            $this->public = $this->payload['public'];
        }

        return $this->public;
    }

    public function name(): SoilmodelName
    {
        if ($this->name === null){
            $this->name = SoilmodelName::fromString($this->payload['name']);
        }

        return $this->name;
    }

    public function description(): SoilmodelDescription
    {
        if ($this->description === null){
            $this->description = SoilmodelDescription::fromString($this->payload['description']);
        }

        return $this->description;
    }

    public function borelogs(): array
    {
        if ($this->borelogs === null){
            $this->borelogs = json_decode($this->payload['borelogs']);
        }

        return $this->borelogs;
    }

    public function layers(): array
    {
        if ($this->layers === null){
            $this->layers = json_decode($this->payload['layers']);
        }

        return $this->layers;
    }
}
