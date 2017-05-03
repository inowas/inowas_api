<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\IdInterface;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\Modelname;
use Prooph\EventSourcing\AggregateChanged;

class ModflowScenarioNameWasChanged extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowId;

    /** @var ModflowId */
    private $scenarioId;

    /** @var Modelname */
    private $name;

    /** @var UserId */
    private $userId;

    public static function byUserWithName(UserId $userId, ModflowId $modflowId, ModflowId $scenarioId, Modelname $name): ModflowScenarioNameWasChanged
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

    public function modflowId(): IdInterface
    {
        if ($this->modflowId === null){
            $this->modflowId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modflowId;
    }

    public function scenarioId(): IdInterface
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

    public function name(): Modelname
    {
        if ($this->name === null){
            $this->name = Modelname::fromString($this->payload['name']);
        }

        return $this->name;
    }
}
