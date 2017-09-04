<?php

declare(strict_types=1);

namespace Inowas\Common\Calculation;

use Inowas\Common\Id\CalculationId;

final class CalculationStateQuery implements \JsonSerializable
{
    /** @var CalculationId */
    private $id;

    /** @var CalculationState  */
    private $state;

    /** @var CalculationMessage  */
    private $message;

    public static function createWithCalculationId(CalculationId $id, CalculationState $state, CalculationMessage $message): CalculationStateQuery
    {
        return new self($id, $state, $message);
    }

    public static function createWithEmptyCalculationId(CalculationState $state): CalculationStateQuery
    {
        return new self(CalculationId::fromString(''), $state, CalculationMessage::fromString(''));
    }

    private function __construct(CalculationId $id, CalculationState $state, CalculationMessage $message) {
        $this->id = $id;
        $this->state = $state;
        $this->message = $message;
    }

    public function toArray(): array
    {
        return array(
            'calculation_id' => $this->id->toString(),
            'state' => $this->state->toInt(),
            'message' => $this->message->toInt()
        );
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
