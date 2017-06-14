<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Event;

use Inowas\Common\Id\UserId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisName;
use Prooph\EventSourcing\AggregateChanged;

class ScenarioAnalysisNameWasChanged extends AggregateChanged
{

    /** @var  ScenarioAnalysisId */
    private $scenarioAnalysisId;

    /** @var ScenarioAnalysisName */
    private $name;

    /** @var UserId */
    private $userId;

    public static function of(ScenarioAnalysisId $id, UserId $userId, ScenarioAnalysisName $name): ScenarioAnalysisNameWasChanged
    {
        $event = self::occur($id->toString(), [
                'user_id' => $userId->toString(),
                'name' => $name->toString()
            ]
        );

        $event->userId = $userId;
        $event->name = $name;

        return $event;
    }

    public function scenarioAnalysisId(): ScenarioAnalysisId
    {
        if ($this->scenarioAnalysisId === null){
            $this->scenarioAnalysisId = ScenarioAnalysisId::fromString($this->aggregateId());
        }

        return $this->scenarioAnalysisId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function name(): ScenarioAnalysisName
    {
        if ($this->name === null){
            $this->name = ScenarioAnalysisName::fromString($this->payload['name']);
        }

        return $this->name;
    }
}
