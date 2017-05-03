<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Command;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateModflowModelCalculation extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserWithModelId(
        ModflowId $calculationId,
        UserId $userId,
        ModflowId $modelId,
        DateTime $startDateTime,
        DateTime $endDateTime
    ): CreateModflowModelCalculation
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'calculation_id' => $calculationId->toString(),
                'modflow_model_id' => $modelId->toString(),
                'start_date_time' => $startDateTime->toAtom(),
                'end_date_time' => $endDateTime->toAtom()
            ]
        );
    }

    public static function byUserWithModelAndScenarioId(
        ModflowId $calculationId,
        UserId $userId,
        ModflowId $modelId,
        ModflowId $scenarioId,
        DateTime $startDateTime,
        DateTime $endDateTime
    ): CreateModflowModelCalculation
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'calculation_id' => $calculationId->toString(),
                'modflow_model_id' => $modelId->toString(),
                'scenario_id' => $scenarioId->toString(),
                'start_date_time' => $startDateTime->toAtom(),
                'end_date_time' => $endDateTime->toAtom()
            ]
        );
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function modflowModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['modflow_model_id']);
    }

    public function calculationId(): ModflowId
    {
        return ModflowId::fromString($this->payload['calculation_id']);
    }

    public function scenarioId(): ?ModflowId
    {
        if (array_key_exists('scenario_id', $this->payload)){
            return ModflowId::fromString($this->payload['scenario_id']);
        }

        return null;
    }

    public function startDateTime(): DateTime
    {
        return DateTime::fromAtom($this->payload['start_date_time']);
    }

    public function endDateTime(): DateTime
    {
        return DateTime::fromAtom($this->payload['end_date_time']);
    }
}
