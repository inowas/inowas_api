<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\ModflowModelName;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelNameWasChanged extends AggregateChanged
{

    /** @var  ModflowId */
    private $ModflowId;

    /** @var ModflowModelName */
    private $name;

    public static function withName(ModflowId $ModflowId, ModflowModelName $name): ModflowModelNameWasChanged
    {
        $event = self::occur(
            $ModflowId->toString(), [
                'name' => $name->toString()
            ]
        );

        $event->ModflowId = $ModflowId;
        $event->name = $name;

        return $event;
    }

    public function ModflowId(): ModflowId
    {
        if ($this->ModflowId === null){
            $this->ModflowId = ModflowId::fromString($this->aggregateId());
        }

        return $this->ModflowId;
    }

    public function name(): ModflowModelName
    {
        if ($this->name === null){
            $this->name = ModflowModelName::fromString($this->payload['name']);
        }

        return $this->name;
    }
}
