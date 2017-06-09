<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\SoilmodelId;
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
     * @param SoilmodelId $newSoilmodelId
     * @param ModflowId $newCalculationId
     * @return CloneModflowModel
     */
    public static function byIdAndCloneSoilmodel(ModflowId $baseModelId, UserId $userId, ModflowId $newModelId, SoilmodelId $newSoilmodelId, ModflowId $newCalculationId): CloneModflowModel
    {
        return new self([
            'basemodel_id' => $baseModelId->toString(),
            'user_id' => $userId->toString(),
            'new_model_id' => $newModelId->toString(),
            'new_soilmodel_id' => $newSoilmodelId->toString(),
            'new_calculation_id' => $newCalculationId->toString()
        ]);
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $baseModelId
     * @param UserId $userId
     * @param ModflowId $newModelId
     * @param SoilmodelId $existingSoilmodelId
     * @param ModflowId $newCalculationId
     * @return CloneModflowModel
     */
    public static function byIdWithExistingSoilmodel(ModflowId $baseModelId, UserId $userId, ModflowId $newModelId, SoilmodelId $existingSoilmodelId, ModflowId $newCalculationId): CloneModflowModel
    {
        return new self([
            'basemodel_id' => $baseModelId->toString(),
            'user_id' => $userId->toString(),
            'new_model_id' => $newModelId->toString(),
            'existing_soilmodel_id' => $existingSoilmodelId->toString(),
            'new_calculation_id' => $newCalculationId->toString()
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

    public function soilModelId(): SoilmodelId
    {
        if ($this->cloneSoilmodel()) {
            return SoilmodelId::fromString($this->payload['new_soilmodel_id']);
        }

        return SoilmodelId::fromString($this->payload['existing_soilmodel_id']);
    }

    public function cloneSoilmodel(): bool
    {
        return array_key_exists('new_soilmodel_id', $this->payload);
    }

    public function newCalculationId(): ModflowId
    {
        return ModflowId::fromString($this->payload['new_calculation_id']);
    }
}
