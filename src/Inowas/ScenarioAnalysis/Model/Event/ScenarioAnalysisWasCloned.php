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
class ScenarioAnalysisWasCloned extends AggregateChanged
{

    /** @var  ScenarioAnalysisId */
    private $fromScenarioAnalysisId;

    /** @var  ScenarioAnalysisId */
    private $scenarioAnalysisId;

    /** @var ModflowId */
    private $baseModelId;

    /** @var  UserId */
    private $userId;

    /** @var ScenarioAnalysisName */
    private $name;

    /** @var ScenarioAnalysisDescription */
    private $description;

    /** @var array */
    private $scenarios;


    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ScenarioAnalysisId $fromId
     * @param ScenarioAnalysisId $id
     * @param UserId $userId
     * @param ModflowId $baseModelId
     * @param ScenarioAnalysisName $name
     * @param ScenarioAnalysisDescription $description
     * @param array $scenarios
     * @return ScenarioAnalysisWasCloned
     */
    public static function byUserWithId(ScenarioAnalysisId $fromId, ScenarioAnalysisId $id, UserId $userId, ModflowId $baseModelId, ScenarioAnalysisName $name, ScenarioAnalysisDescription $description, array $scenarios): ScenarioAnalysisWasCloned
    {
        $event = self::occur($id->toString(),[
            'from_id' => $fromId->toString(),
            'user_id' => $userId->toString(),
            'basemodel_id' => $baseModelId->toString(),
            'name' => $name->toString(),
            'description' => $description->toString(),
            'scenarios' => json_encode($scenarios),
        ]);

        $event->fromScenarioAnalysisId = $fromId;
        $event->scenarioAnalysisId = $id;
        $event->userId = $userId;
        $event->baseModelId = $baseModelId;
        $event->name = $name;
        $event->description = $description;
        $event->scenarios = $scenarios;

        return $event;
    }

    public function scenarioAnalysisId(): ScenarioAnalysisId
    {
        if ($this->scenarioAnalysisId === null){
            $this->scenarioAnalysisId = ScenarioAnalysisId::fromString($this->aggregateId());
        }

        return $this->scenarioAnalysisId;
    }

    public function fromScenarioAnalysisId(): ScenarioAnalysisId
    {
        if ($this->fromScenarioAnalysisId === null){
            $this->fromScenarioAnalysisId = ScenarioAnalysisId::fromString($this->payload['from_id']);
        }

        return $this->fromScenarioAnalysisId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function baseModelId(): ModflowId
    {
        if ($this->baseModelId === null){
            $this->baseModelId = ModflowId::fromString($this->payload['basemodel_id']);
        }

        return $this->baseModelId;
    }

    public function name(): ScenarioAnalysisName
    {
        if ($this->name === null){
            $this->name = ScenarioAnalysisName::fromString($this->payload['name']);
        }

        return $this->name;
    }

    public function description(): ScenarioAnalysisDescription
    {
        if ($this->description === null){
            $this->description = ScenarioAnalysisDescription::fromString($this->payload['description']);
        }

        return $this->description;
    }

    public function scenarios(): array
    {
        if ($this->scenarios === null){
            $this->scenarios = json_decode($this->payload['scenarios']);
        }

        return $this->scenarios;
    }
}
