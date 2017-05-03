<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateCalculationPackageParameter extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserWithModelId(
        ModflowId $calculationId,
        UserId $userId,
        ModflowId $modelId,
        string $packageName,
        string $parameterName,
        $payload
    ): UpdateCalculationPackageParameter
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'calculation_id' => $calculationId->toString(),
                'modflow_model_id' => $modelId->toString(),
                'package_name' => $packageName,
                'parameter_name' => $parameterName,
                'payload' => serialize($payload)
            ]
        );
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function calculationId(): ModflowId
    {
        return ModflowId::fromString($this->payload['calculation_id']);
    }

    public function modflowModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['modflow_model_id']);
    }

    public function packageName(): string
    {
        return $this->payload['package_name'];
    }

    public function parameterName(): string
    {
        return $this->payload['parameter_name'];
    }

    public function payload()
    {
        return unserialize($this->payload['payload']);
    }
}
