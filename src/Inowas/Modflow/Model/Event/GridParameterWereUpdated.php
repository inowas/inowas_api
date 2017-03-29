<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Prooph\EventSourcing\AggregateChanged;

class GridParameterWereUpdated extends AggregateChanged
{
    /** @var ModflowId */
    private $calculationId;

    /** @var  BoundingBox */
    private $boundingBox;

    /** @var  GridSize */
    private $gridSize;

    public static function to(
        ModflowId $calculationId,
        GridSize $gridSize,
        BoundingBox $boundingBox
    ): GridParameterWereUpdated
    {
        $event = self::occur($calculationId->toString(),[
            'grid_size' => $gridSize->toArray(),
            'bounding_box' => $boundingBox->toArray()
        ]);

        return $event;
    }

    public function calculationId(): ModflowId
    {
        if ($this->calculationId === null){
            $this->calculationId = ModflowId::fromString($this->aggregateId());
        }

        return $this->calculationId;
    }

    public function boundingBox(): BoundingBox
    {
        if ($this->boundingBox === null) {
            $this->boundingBox = BoundingBox::fromArray($this->payload['bounding_box']);
        }

        return $this->boundingBox;
    }

    public function gridSize(): GridSize
    {
        if ($this->gridSize === null) {
            $this->gridSize = GridSize::fromArray($this->payload['grid_size']);
        }

        return $this->gridSize;
    }
}
