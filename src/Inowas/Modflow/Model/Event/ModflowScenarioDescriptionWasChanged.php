<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\ModflowIdInterface;
use Inowas\Modflow\Model\ModflowModelDescription;
use Inowas\Modflow\Model\UserId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowScenarioDescriptionWasChanged extends AggregateChanged
{

    /** @var  ModflowIdInterface */
    private $modflowId;

    /** @var  ModflowIdInterface */
    private $scenarioId;

    /** @var ModflowModelDescription */
    private $description;

    /** @var  UserId */
    private $userId;

    public static function byUserWithName(UserId $userId, ModflowId $modflowId, ModflowId $scenarioId, ModflowModelDescription $description): ModflowScenarioDescriptionWasChanged
    {
        $event = self::occur(
            $modflowId->toString(), [
                'user_id' => $userId->toString(),
                'scenario_id' => $scenarioId->toString(),
                'description' => $description->toString()
            ]
        );

        $event->userId = $userId;
        $event->modflowId = $modflowId;
        $event->scenarioId = $scenarioId;
        $event->description = $description;

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

    public function description(): ModflowModelDescription
    {
        if ($this->description === null){
            $this->description = ModflowModelDescription::fromString($this->payload['description']);
        }

        return $this->description;
    }
}
