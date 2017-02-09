<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowModelDescription;
use Inowas\Modflow\Model\ModflowModelId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelDescriptionWasChanged extends AggregateChanged
{

    /** @var  ModflowModelId */
    private $modflowModelId;

    /** @var ModflowModelDescription */
    private $description;

    public static function withDescription(ModflowModelId $modflowModelId, ModflowModelDescription $description): ModflowModelDescriptionWasChanged
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'description' => $description->toString()
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->description = $description;

        return $event;
    }

    public function modflowModelId(): ModflowModelId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowModelId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function description(): ModflowModelDescription
    {
        if ($this->description === null){
            $this->description = ModflowModelDescription::fromString($this->payload['description']);
        }

        return $this->description;
    }
}
