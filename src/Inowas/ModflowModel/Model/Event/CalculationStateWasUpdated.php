<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Calculation\CalculationState;
use Inowas\Common\Id\CalculationId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\ModflowModel\Model\AMQP\ModflowCalculationResponse;
use Prooph\EventSourcing\AggregateChanged;

class CalculationStateWasUpdated extends AggregateChanged
{
    /** @var  ModflowId */
    private $modelId;

    /** @noinspection MoreThanThreeArgumentsInspection */
    public static function withParams(?UserId $userId, ModflowId $modflowId, ?CalculationId $calculationId, ?CalculationState $state, ?ModflowCalculationResponse $response): self
    {
        $payload = [];

        if ($userId instanceof UserId) {
            $payload['user_id'] = $userId->toString();
        }

        if ($calculationId instanceof CalculationId) {
            $payload['calculation_id'] = $calculationId->toString();
        }

        if ($state instanceof CalculationState) {
            $payload['state'] = $state->toInt();
        }

        if ($response instanceof ModflowCalculationResponse) {
            $payload['response'] = $response->toArray();
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::occur($modflowId->toString(), $payload);
    }

    public function modelId(): ModflowId
    {
        if ($this->modelId === null) {
            $this->modelId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modelId;
    }

    public function calculationId(): ?CalculationId
    {
        if (!\array_key_exists('calculation_id', $this->payload)) {
            return null;
        }

        return CalculationId::fromString($this->payload['calculation_id']);
    }

    public function state(): CalculationState
    {
        return CalculationState::fromInt($this->payload['state']);
    }

    public function userId(): ?UserId
    {
        if (!\array_key_exists('user_id', $this->payload)) {
            return null;
        }

        return UserId::fromString($this->payload['user_id']);
    }


    public function response(): ?ModflowCalculationResponse
    {
        if (!\array_key_exists('response', $this->payload)) {
            return null;
        }

        return ModflowCalculationResponse::fromArray($this->payload['response']);
    }
}
