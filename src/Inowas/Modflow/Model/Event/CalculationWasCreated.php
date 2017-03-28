<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Soilmodel\Model\SoilmodelId;
use Prooph\EventSourcing\AggregateChanged;

class CalculationWasCreated extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    /** @var  ModflowId */
    private $modflowModelId;

    /** @var  SoilmodelId */
    private $soilModelId;

    /** @var UserId */
    private $userId;

    /** @var  GridSize */
    private $gridSize;

    /** @var  BoundingBox */
    private $boundingBox;

    /** @var  TimeUnit */
    private $timeUnit;

    /** @var  LengthUnit */
    private $lengthUnit;

    /** @var  DateTime */
    private $startDateTime;

    /** @var  DateTime */
    private $endDateTime;

    public static function fromModel(
        UserId $userId,
        ModflowId $calculationId,
        ModflowId $modflowModelId,
        SoilmodelId $soilModelId,
        GridSize $gridSize,
        BoundingBox $boundingBox,
        TimeUnit $timeUnit,
        LengthUnit $lengthUnit,
        DateTime $startDateTime,
        DateTime $endDateTime
    ): CalculationWasCreated
    {
        $event = self::occur($calculationId->toString(),[
            'user_id' => $userId->toString(),
            'modflowmodel_id' => $modflowModelId->toString(),
            'soilmodel_id' => $soilModelId->toString(),
            'grid_size' => $gridSize->toArray(),
            'time_unit' => $timeUnit->toValue(),
            'length_unit' => $lengthUnit->toValue(),
            'bounding_box' => $boundingBox->toArray(),
            'start_date_time' => $startDateTime->toAtom(),
            'end_date_time' => $endDateTime->toAtom()
        ]);

        $event->calculationId = $calculationId;
        $event->modflowModelId = $modflowModelId;
        $event->soilModelId = $soilModelId;
        $event->userId = $userId;
        $event->gridSize = $gridSize;
        $event->timeUnit = $timeUnit;
        $event->lengthUnit = $lengthUnit;
        $event->boundingBox = $boundingBox;
        $event->startDateTime = $startDateTime;
        $event->endDateTime = $endDateTime;

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

    public function soilModelId(): SoilmodelId
    {
        if ($this->soilModelId === null){
            $this->soilModelId = SoilmodelId::fromString($this->payload['soilmodel_id']);
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

    public function timeUnit(): TimeUnit
    {
        if ($this->timeUnit === null){
            $this->timeUnit = TimeUnit::fromValue($this->payload['time_unit']);
        }

        return $this->timeUnit;
    }

    public function lengthUnit(): LengthUnit
    {
        if ($this->lengthUnit === null){
            $this->lengthUnit = LengthUnit::fromValue($this->payload['length_unit']);
        }

        return $this->lengthUnit;
    }

    public function boundingBox(): BoundingBox
    {
        if ($this->boundingBox === null){
            $this->boundingBox = BoundingBox::fromArray($this->payload['bounding_box']);
        }

        return $this->boundingBox;
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
