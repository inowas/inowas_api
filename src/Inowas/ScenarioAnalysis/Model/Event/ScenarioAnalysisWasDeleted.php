<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisDescription;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisName;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class ScenarioAnalysisWasDeleted extends AggregateChanged
{

    /** @var  ScenarioAnalysisId */
    private $scenarioAnalysisId;

    /** @var  UserId */
    private $userId;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ScenarioAnalysisId $id
     * @param UserId $userId
     * @return ScenarioAnalysisWasDeleted
     */
    public static function byUserWithId(ScenarioAnalysisId $id, UserId $userId): ScenarioAnalysisWasDeleted
    {
        $event = self::occur($id->toString(),[
            'user_id' => $userId->toString()
        ]);

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

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
