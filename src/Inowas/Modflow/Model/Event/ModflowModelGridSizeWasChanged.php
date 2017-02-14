<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowModelGridSize;
use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\UserId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelGridSizeWasChanged extends AggregateChanged
{

    /** @var  ModflowModelId */
    private $modflowModelId;

    /** @var ModflowModelGridSize */
    private $gridSize;

    /** @var  UserId */
    private $userId;

    public static function withGridSize(UserId $userId, ModflowModelId $modflowModelId, ModflowModelGridSize $gridSize): ModflowModelGridSizeWasChanged
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'user_id' => $userId->toString(),
                'grid_size' => [
                    'nX' => $gridSize->nX(),
                    'nY' => $gridSize->nY()
                ]
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->gridSize = $gridSize;

        return $event;
    }

    public function modflowModelId(): ModflowModelId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowModelId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function gridSize(): ModflowModelGridSize
    {
        if ($this->gridSize === null){
            $this->gridSize = ModflowModelGridSize::fromXY($this->payload['grid_size']['nX'], $this->payload['grid_size']['nY']);
        }

        return $this->gridSize;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
