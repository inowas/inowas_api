<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Command\AbstractJsonSchemaCommand;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\Mt3dms;

class UpdateMt3dms extends AbstractJsonSchemaCommand
{

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $modelId
     * @param Mt3dms $mt3dms
     * @return UpdateMt3dms
     */
    public static function byUserModelIdAndPackageData(
        UserId $userId,
        ModflowId $modelId,
        Mt3dms $mt3dms
    ): UpdateMt3dms
    {
        $self = new static([
            'id' => $modelId->toString(),
            'mt3dms' => $mt3dms->toArray()
        ]);

        /** @var UpdateMt3dms $self */
        $self = $self->withAddedMetadata('user_id', $userId->toString());
        return $self;
    }

    public function schema(): string
    {
        return 'file://spec/schema/modflow/command/updateMt3dmsPayload.json';
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->metadata['user_id']);
    }

    public function modelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['id']);
    }

    public function data(): Mt3dms
    {
        return Mt3dms::fromArray($this->payload['mt3dms']);
    }
}
