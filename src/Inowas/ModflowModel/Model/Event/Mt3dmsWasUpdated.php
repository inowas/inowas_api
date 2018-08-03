<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\Mt3dms;
use Prooph\EventSourcing\AggregateChanged;

class Mt3dmsWasUpdated extends AggregateChanged
{
    /** @var ModflowId */
    private $modelId;

    /** @var UserId */
    private $userId;

    /** @var Mt3dms */
    private $mt3dms;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $modelId
     * @param Mt3dms $mt3dms
     * @return Mt3dmsWasUpdated
     */
    public static function withProps(UserId $userId, ModflowId $modelId, Mt3dms $mt3dms): Mt3dmsWasUpdated
    {
        /** @var Mt3dmsWasUpdated $event */
        $event = self::occur($modelId->toString(), [
            'user_id' => $userId->toString(),
            'mt3dms' => $mt3dms->toArray()
        ]);

        $event->userId = $userId;
        $event->modelId = $modelId;
        $event->mt3dms = $mt3dms;
        return $event;
    }

    public function modelId(): ModflowId
    {
        if ($this->modelId === null) {
            $this->modelId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modelId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null) {
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function mt3dms(): Mt3dms
    {
        if ($this->mt3dms === null) {
            $this->mt3dms = Mt3dms::fromArray($this->payload['mt3dms']);
        }

        return $this->mt3dms;
    }
}
