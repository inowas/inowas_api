<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowModelId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelWasCreated extends AggregateChanged
{

    /** @var  ModflowModelId */
    private $modflowModelId;

    public static function withId(ModflowModelId $modflowModelId): ModflowModelWasCreated
    {
        $event = self::occur($modflowModelId->toString());
        $event->modflowModelId = $modflowModelId;

        return $event;
    }

    public function modflowModelId(): ModflowModelId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowModelId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }
}
