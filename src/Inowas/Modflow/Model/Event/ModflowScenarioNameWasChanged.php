<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\ModflowIdInterface;
use Inowas\Modflow\Model\ModflowModelName;
use Inowas\Modflow\Model\UserId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowScenarioNameWasChanged extends AggregateChanged
{

    /** @var  ModflowIdInterface */
    private $modflowId;

    /** @var  ModflowIdInterface */
    private $scenarioId;

    /** @var ModflowModelName */
    private $name;

    /** @var  UserId */
    private $userId;

    public static function byUserWithName(UserId $userId, ModflowId $modflowId, ModflowId $scenarioId, ModflowModelName $name): ModflowScenarioNameWasChanged
    {
        $event = self::occur(
            $modflowId->toString(), [
                'user_id' => $userId->toString(),
                'scenario_id' => $scenarioId->toString(),
                'name' => $name->toString()
            ]
        );

        $event->userId = $userId;
        $event->modflowId = $modflowId;
        $event->scenarioId = $scenarioId;
        $event->name = $name;

        return $event;
    }

    public function modflowId(): ModflowIdInterface
    {
        if ($this->modflowId === null){
            $this->modflowId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modflowId;
    }

    public function scenarioId(): ModflowIdInterface
    {
        if ($this->scenarioId === null){
            $this->scenarioId = ModflowId::fromString($this->payload['scenario_id']);
        }

        return $this->scenarioId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function name(): ModflowModelName
    {
        if ($this->name === null){
            $this->name = ModflowModelName::fromString($this->payload['name']);
        }

        return $this->name;
    }
}
