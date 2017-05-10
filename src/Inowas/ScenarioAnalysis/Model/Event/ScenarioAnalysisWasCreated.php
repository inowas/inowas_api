<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Id\IdInterface;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Prooph\EventSourcing\AggregateChanged;

class ScenarioAnalysisWasCreated extends AggregateChanged
{

    /** @var  ScenarioAnalysisId */
    private $scenarioAnalysisId;

    /** @var ModflowId */
    private $baseModelId;

    /** @var  UserId */
    private $userId;

    public static function byUserWithId(ScenarioAnalysisId $id, UserId $userId, ModflowId $baseModelId): ScenarioAnalysisWasCreated
    {
        $event = self::occur($id->toString(),[
            'basemodel_id' => $baseModelId->toString(),
            'user_id' => $userId->toString()
        ]);

        $event->baseModelId = $baseModelId;
        $event->userId = $userId;

        return $event;
    }

    public function scenarioAnalysisId(): ScenarioAnalysisId
    {
        if ($this->scenarioAnalysisId === null){
            $this->scenarioAnalysisId = ScenarioAnalysisId::fromString($this->aggregateId());
        }

        return $this->scenarioAnalysisId();
    }

    public function baseModelId(): ModflowId
    {
        if ($this->baseModelId === null){
            $this->baseModelId = ModflowId::fromString($this->payload['basemodel_id']);
        }

        return $this->baseModelId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
