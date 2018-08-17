<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\ModflowModel\Model\AMQP\ModflowOptimizationResponse;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateOptimizationProgress extends Command implements PayloadConstructable
{

    use PayloadTrait;

    /**
     * @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowOptimizationResponse $response
     * @return UpdateOptimizationProgress
     */
    public static function withProgressUpdate(ModflowOptimizationResponse $response): self
    {
        $payload = [
            'response' => $response->toArray()
        ];

        return new self($payload);
    }

    public function response(): ModflowOptimizationResponse
    {
        return ModflowOptimizationResponse::fromArray($this->payload['response']);
    }
}
