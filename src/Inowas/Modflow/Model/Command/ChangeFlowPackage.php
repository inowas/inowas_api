<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\PackageName;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeFlowPackage extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserWithCalculationId(
        UserId $userId,
        ModflowId $calculationId,
        PackageName $packageName
    ): ChangeFlowPackage
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'calculation_id' => $calculationId->toString(),
                'package_name' => $packageName->toString()
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

    public function packageName(): PackageName
    {
        return PackageName::fromString($this->payload['package_name']);
    }
}
