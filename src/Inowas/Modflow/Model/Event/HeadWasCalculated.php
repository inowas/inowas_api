<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\FileName;
use Inowas\Common\LayerNumber;
use Inowas\Modflow\Model\ResultType;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\TotalTime;
use Prooph\EventSourcing\AggregateChanged;

class HeadWasCalculated extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    /** @var  ResultType $type */
    protected $type;

    /** @var  TotalTime */
    protected $totalTime;

    /** @var  LayerNumber */
    protected $layerNumber;

    /** @var  FileName */
    protected $filename;

    public static function to(
        ModflowId $calculationId,
        ResultType $type,
        TotalTime $totalTime,
        LayerNumber $layerNumber,
        FileName $fileName
    ): HeadWasCalculated
    {
        $event = self::occur($calculationId->toString(),[
            'type' => $type->toString(),
            'total_time' => $totalTime->toInteger(),
            'layer' => $layerNumber->toInteger(),
            'filename' => $fileName->toString()
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

    public function type(): ResultType
    {
        if ($this->type === null){
            $this->type = ResultType::fromString($this->payload['type']);
        }

        return $this->type;
    }

    public function totalTime(): TotalTime
    {
        if ($this->totalTime === null) {
            $this->totalTime = TotalTime::fromInt($this->payload['total_time']);
        }

        return $this->totalTime;
    }

    public function layer(): LayerNumber
    {
        if ($this->layerNumber === null){
            $this->layerNumber = LayerNumber::fromInteger($this->payload['layer']);
        }

        return $this->layerNumber;
    }

    public function filename(): FileName
    {
        if ($this->filename === null){
            $this->filename = FileName::fromString($this->payload['filename']);
        }

        return $this->filename;
    }
}
