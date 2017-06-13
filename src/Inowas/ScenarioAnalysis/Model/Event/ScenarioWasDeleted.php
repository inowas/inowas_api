<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class ScenarioWasDeleted extends AggregateChanged
{

    /** @var  ScenarioAnalysisId */
    private $scenarioAnalysisId;

    /** @var  ModflowId */
    private $scenarioId;

    /** @var  UserId */
    protected $userId;

    public static function from(ScenarioAnalysisId $id, UserId $userId, ModflowId $scenarioId): ScenarioWasDeleted
    {
        $event = self::occur($id->toString(), [
            'scenario_id' => $scenarioId->toString(),
            'user_id' => $userId->toString(),
        ]);

        $event->scenarioId = $scenarioId;
        $event->userId = $userId;

        return $event;
    }

    public function scenarioAnalysisId(): ScenarioAnalysisId
    {
        if ($this->scenarioAnalysisId === null){
            $this->scenarioAnalysisId = ScenarioAnalysisId::fromString($this->aggregateId());
        }

        return $this->scenarioAnalysisId;
    }

    public function scenarioId(): ModflowId
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
}
