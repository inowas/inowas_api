<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\ModelDescription;
use Inowas\Common\Modflow\ModelName;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class ScenarioWasCloned extends AggregateChanged
{

    /** @var  ScenarioAnalysisId */
    private $scenarioAnalysisId;

    /** @var  ModflowId */
    private $newScenarioId;

    /** @var  ModflowId */
    private $baseScenarioId;

    /** @var  UserId */
    protected $userId;

    /** @var  ModelName */
    protected $name;

    /** @var  ModelDescription */
    protected $description;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ScenarioAnalysisId $id
     * @param UserId $userId
     * @param ModflowId $scenarioId
     * @param ModflowId $baseModelId
     * @param ModelName $name
     * @param ModelDescription $description
     * @return ScenarioWasCloned
     */
    public static function from(ScenarioAnalysisId $id, UserId $userId, ModflowId $scenarioId, ModflowId $baseModelId, ModelName $name, ModelDescription $description): ScenarioWasCloned
    {
        $event = self::occur($id->toString(), [
            'scenario_id' => $scenarioId->toString(),
            'basemodel_id' => $baseModelId->toString(),
            'user_id' => $userId->toString(),
            'name' => $name->toString(),
            'description' => $description->toString()
        ]);

        $event->newScenarioId = $scenarioId;
        $event->userId = $userId;
        $event->name = $name;
        $event->description = $description;
        $event->baseScenarioId = $baseModelId;


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
        if ($this->newScenarioId === null){
            $this->newScenarioId = ModflowId::fromString($this->payload['scenario_id']);
        }

        return $this->newScenarioId;
    }

    public function baseModelId(): ModflowId
    {
        if ($this->baseScenarioId === null){
            $this->baseScenarioId = ModflowId::fromString($this->payload['basemodel_id']);
        }

        return $this->baseScenarioId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function name(): ModelName
    {
        if ($this->name === null){
            $this->name = ModelName::fromString($this->payload['name']);
        }

        return $this->name;
    }

    public function description(): ModelDescription
    {
        if ($this->description === null){
            $this->description = ModelDescription::fromString($this->payload['description']);
        }

        return $this->description;
    }
}
