<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\PackageName;
use Prooph\EventSourcing\AggregateChanged;

class CalculationFlowPackageWasChanged extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    /** @var  UserId */
    private $userId;

    /** @var  PackageName */
    private $packageName;

    public static function to(
        UserId $userId,
        ModflowId $calculationId,
        PackageName $packageName
    ): CalculationFlowPackageWasChanged
    {
        $event = self::occur($calculationId->toString(),[
            'user_id' => $userId->toString(),
            'package_name' => $packageName->toString(),
        ]);

        $event->userId = $userId;
        $event->calculationId = $calculationId;
        $event->packageName = $packageName;
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

    public function packageName(): PackageName
    {
        if ($this->packageName === null){
            $this->packageName = PackageName::fromString($this->payload['package_name']);
        }
        return $this->packageName;
    }
}
