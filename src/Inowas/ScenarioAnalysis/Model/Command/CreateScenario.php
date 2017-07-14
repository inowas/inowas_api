<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\Name;
use Inowas\Common\Modflow\Description;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateScenario extends Command implements PayloadConstructable
{

    use PayloadTrait;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ScenarioAnalysisId $scenarioAnalysisId
     * @param UserId $userId
     * @param ModflowId $baseModelId
     * @param ModflowId $scenarioId
     * @param Name $name
     * @param Description $description
     * @return CreateScenario
     */
    public static function byUserWithBaseModelAndScenarioIdAndName(
        ScenarioAnalysisId $scenarioAnalysisId,
        UserId $userId,
        ModflowId $baseModelId,
        ModflowId $scenarioId,
        Name $name,
        Description $description
    ): CreateScenario
    {
        return new self([
            'scenarioanalysis_id' => $scenarioAnalysisId->toString(),
            'user_id' => $userId->toString(),
            'basemodel_id' => $baseModelId->toString(),
            'scenario_id' => $scenarioId->toString(),
            'name' => $name->toString(),
            'description' => $description->toString()
        ]);
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ScenarioAnalysisId $scenarioAnalysisId
     * @param UserId $userId
     * @param ModflowId $baseModelId
     * @param ModflowId $scenarioId
     * @param string $prefix
     * @return CreateScenario
     */
    public static function byUserWithBaseModelAndScenarioIdAndPrefix(
        ScenarioAnalysisId $scenarioAnalysisId,
        UserId $userId,
        ModflowId $baseModelId,
        ModflowId $scenarioId,
        string $prefix
    ): CreateScenario
    {
        return new self([
            'scenarioanalysis_id' => $scenarioAnalysisId->toString(),
            'user_id' => $userId->toString(),
            'basemodel_id' => $baseModelId->toString(),
            'scenario_id' => $scenarioId->toString(),
            'prefix' => $prefix
        ]);
    }

    public function scenarioAnalysisId(): ScenarioAnalysisId
    {
        return ScenarioAnalysisId::fromString($this->payload['scenarioanalysis_id']);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function baseModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['basemodel_id']);
    }

    public function scenarioId(): ModflowId
    {
        return ModflowId::fromString($this->payload['scenario_id']);
    }

    public function name(): Name
    {
        if (array_key_exists('name', $this->payload)) {
            return Name::fromString($this->payload['name']);
        }

        return Name::fromString($this->payload['']);
    }

    public function description(): Description
    {
        if (array_key_exists('description', $this->payload)) {
            return Description::fromString($this->payload['description']);
        }

        return Description::fromString($this->payload['description']);
    }

    public function hasPrefix(): bool
    {
        return array_key_exists('prefix', $this->payload);
    }

    public function prefix(): string
    {
        return $this->payload['prefix'];
    }

}
