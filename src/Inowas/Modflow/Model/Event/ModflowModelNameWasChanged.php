<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\ModflowModelName;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelNameWasChanged extends AggregateChanged
{

    /** @var  ModflowModelId */
    private $modflowModelId;

    /** @var ModflowModelName */
    private $name;

    public static function withName(ModflowModelId $modflowModelId, ModflowModelName $name): ModflowModelNameWasChanged
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'name' => $name->toString()
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->name = $name;

        return $event;
    }

    public function modflowModelId(): ModflowModelId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowModelId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function name(): ModflowModelName
    {
        if ($this->name === null){
            $this->name = ModflowModelName::fromString($this->payload['name']);
        }

        return $this->name;
    }
}
