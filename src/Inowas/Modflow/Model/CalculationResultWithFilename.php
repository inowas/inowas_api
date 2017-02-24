<?php

namespace Inowas\Modflow\Model;

use Inowas\Common\FileName;

class CalculationResultWithFilename
{

    /** @var  CalculationResultType */
    protected $type;

    /** @var  TotalTime */
    protected $totalTime;

    /** @var  LayerNumber */
    protected $layerNumber;

    /** @var  FileName */
    protected $filename;

    public static function fromParameters(CalculationResultType $type, TotalTime $totalTime, LayerNumber $layerNumber, FileName $filename): CalculationResultWithFilename
    {
        $self = new self();
        $self->type = $type;
        $self->totalTime = $totalTime;
        $self->layerNumber = $layerNumber;
        $self->filename = $filename;
        return $self;
    }

    public function type(): CalculationResultType
    {
        return $this->type;
    }

    public function totalTime(): TotalTime
    {
        return $this->totalTime;
    }

    public function layerNumber(): LayerNumber
    {
        return $this->layerNumber;
    }

    public function filename(): FileName
    {
        return $this->filename;
    }
}
