<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\FileName;
use Inowas\Modflow\Model\CalculationResultType;
use Inowas\Modflow\Model\CalculationResultWithFilename;
use Inowas\Modflow\Model\LayerNumber;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\TotalTime;
use Prooph\EventSourcing\AggregateChanged;

class ModflowCalculationResultWasAdded extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    /** @var  CalculationResultWithFilename */
    private $result;

    /** @var  CalculationResultType $type */
    protected $type;

    public static function to(ModflowId $calculationId, CalculationResultWithFilename $result): ModflowCalculationResultWasAdded
    {
        $event = self::occur($calculationId->toString(),[
            'type' => $result->type()->toString(),
            'total_time' => $result->totalTime()->toInteger(),
            'layer' => $result->layerNumber()->toInteger(),
            'filename' => $result->filename()->toString()
        ]);

        $event->result = $result;
        return $event;
    }

    public function calculationId(): ModflowId
    {
        if ($this->calculationId === null){
            $this->calculationId = ModflowId::fromString($this->aggregateId());
        }

        return $this->calculationId;
    }

    public function result(): CalculationResultWithFilename
    {
        if ($this->result === null){
            $this->result = CalculationResultWithFilename::fromParameters(
                CalculationResultType::fromString($this->payload['type']),
                TotalTime::fromInt($this->payload['total_time']),
                LayerNumber::fromInteger($this->payload['layer']),
                FileName::fromString($this->payload['filename'])
            );
        }

        return $this->result;
    }
}
