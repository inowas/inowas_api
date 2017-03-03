<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\SoilModelId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class CalculationWasCreated extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    /** @var  ModflowId */
    private $modflowModelId;

    /** @var  \Inowas\Common\Id\SoilModelId */
    private $soilModelId;

    /** @var  \Inowas\Common\Id\UserId */
    private $userId;

    /** @var  GridSize */
    private $gridSize;

    /** @var  DateTime */
    private $startDateTime;

    /** @var  DateTime */
    private $endDateTime;

    public static function fromModel(
        UserId $userId,
        ModflowId $calculationId,
        ModflowId $modflowModelId,
        SoilModelId $soilModelId,
        GridSize $gridSize,
        DateTime $startDateTime,
        DateTime $endDateTime
    ): CalculationWasCreated
    {
        $event = self::occur($calculationId->toString(),[
            'user_id' => $userId->toString(),
            'modflowmodel_id' => $modflowModelId->toString(),
            'soilmodel_id' => $soilModelId->toString(),
            'grid_size' => $gridSize->toArray(),
            'start_date_time' => $startDateTime->toAtom(),
            'end_date_time' => $endDateTime->toAtom()
        ]);

        $event->modflowModelId = $modflowModelId;
        $event->userId = $userId;
        $event->soilModelId = $soilModelId;

        return $event;
    }

    public function calculationId(): ModflowId
    {
        if ($this->calculationId === null){
            $this->calculationId = ModflowId::fromString($this->aggregateId());
        }

        return $this->calculationId;
    }


    public function modflowModelId(): ModflowId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowId::fromString($this->payload['modflowmodel_id']);
        }

        return $this->modflowModelId;
    }

    public function soilModelId(): SoilModelId
    {
        if ($this->soilModelId === null){
            $this->soilModelId = SoilModelId::fromString($this->payload['soilmodel_id']);
        }

        return $this->soilModelId;
    }

    public function userId(): UserId{
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function gridSize(): GridSize
    {
        if ($this->gridSize === null){
            $this->gridSize = GridSize::fromArray($this->payload['grid_size']);
        }

        return $this->gridSize;
    }

    public function startDateTime(): DateTime
    {
        if ($this->startDateTime === null){
            $this->startDateTime = DateTime::fromAtom($this->payload['start_date_time']);
        }

        return $this->startDateTime;
    }

    public function endDateTime(): DateTime
    {
        if ($this->endDateTime === null){
            $this->endDateTime = DateTime::fromAtom($this->payload['end_date_time']);
        }

        return $this->endDateTime;
    }
}
