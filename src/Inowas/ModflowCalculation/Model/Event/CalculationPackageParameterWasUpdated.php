<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class CalculationPackageParameterWasUpdated extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    /** @var  UserId */
    private $userId;

    /** @var  string */
    private $packageName;

    /** @var  string */
    private $parameterName;

    private $parameterData;

    public static function withProps(
        UserId $userId,
        ModflowId $calculationId,
        $packageName,
        $parameterName,
        $parameterData
    ): CalculationPackageParameterWasUpdated
    {
        $event = self::occur($calculationId->toString(),[
            'user_id' => $userId->toString(),
            'package_name' => $packageName,
            'parameter_name' => $parameterName,
            'parameter_data' => serialize($parameterData)
        ]);

        $event->userId = $userId;
        $event->calculationId = $calculationId;
        $event->packageName = $packageName;
        $event->parameterName = $parameterName;
        $event->parameterData = $parameterData;
        return $event;
    }

    public function calculationId(): ModflowId
    {
        if ($this->calculationId === null){
            $this->calculationId = ModflowId::fromString($this->aggregateId());
        }

        return $this->calculationId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function packageName(): string
    {
        if ($this->packageName === null){
            $this->packageName = $this->payload['package_name'];
        }
        return $this->packageName;
    }

    public function parameterName(): string
    {
        if ($this->parameterName === null){
            $this->parameterName = $this->payload['parameter_name'];
        }
        return $this->parameterName;
    }

    public function parameterData()
    {
        if ($this->parameterData === null){
            $this->parameterData = unserialize($this->payload['parameter_data']);
        }

        return $this->parameterData;
    }
}
