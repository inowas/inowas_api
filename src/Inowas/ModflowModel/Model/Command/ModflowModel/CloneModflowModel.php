<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command\ModflowModel;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CloneModflowModel extends Command implements PayloadConstructable
{

    use PayloadTrait;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $baseModelId
     * @param UserId $userId
     * @param ModflowId $newModelId
     * @return CloneModflowModel
     */
    public static function byId(ModflowId $baseModelId, UserId $userId, ModflowId $newModelId): CloneModflowModel
    {
        return new self([
            'basemodel_id' => $baseModelId->toString(),
            'user_id' => $userId->toString(),
            'new_model_id' => $newModelId->toString(),
            'clone_soilmodel' => true
        ]);
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $baseModelId
     * @param UserId $userId
     * @param ModflowId $newModelId
     * @return CloneModflowModel
     */
    public static function byIdWithoutSoilmodel(ModflowId $baseModelId, UserId $userId, ModflowId $newModelId): CloneModflowModel
    {
        return new self([
            'basemodel_id' => $baseModelId->toString(),
            'user_id' => $userId->toString(),
            'new_model_id' => $newModelId->toString(),
            'clone_soilmodel' => false
        ]);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function baseModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['basemodel_id']);
    }

    public function newModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['new_model_id']);
    }

    public function cloneSoilmodel(): bool
    {
        return $this->payload['clone_soilmodel'];
    }
}
