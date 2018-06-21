<?php
/**
 * backflag : int
 * is a flag used to specify whether residual control will be used. A value of 1
 * indicates that residual control is active and a value of 0 indicates residual
 * control is inactive. (default is 1).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

use Inowas\Common\Grid\Ncol;
use Inowas\Common\Grid\Nlay;
use Inowas\Common\Grid\Nrow;

class HeadObservation implements \JsonSerializable
{
    /** @var Tomulth */
    private $tomulth;

    /** @var Obsname */
    private $obsname;

    /** @var Nlay */
    private $layer;

    /** @var Nrow */
    private $row;

    /** @var Ncol */
    private $column;

    /** @var Roff */
    private $roff;

    /** @var Coff */
    private $coff;

    /** @var Itt */
    private $itt;

    /** @var TimeSeriesData */
    private $timeSeriesData;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param Obsname $obsname
     * @param Nlay $layer
     * @param Nrow $row
     * @param Ncol $column
     * @param TimeSeriesData $timeSeriesData
     * @return HeadObservation
     */
    public static function fromNameLayerRowColumnAndTimeSeriesData(
        Obsname $obsname,
        Nlay $layer,
        Nrow $row,
        Ncol $column,
        TimeSeriesData $timeSeriesData
    ): HeadObservation
    {
        $self = new self();
        $self->obsname = $obsname;
        $self->layer = $layer;
        $self->row = $row;
        $self->column = $column;
        $self->timeSeriesData = $timeSeriesData;
        return $self;
    }

    public static function fromArray(array $arr): HeadObservation
    {
        $self = new self();
        $self->tomulth = Tomulth::fromFloat($arr['tomulth']);
        $self->obsname = Obsname::fromString($arr['obsname']);
        $self->layer = Nlay::fromInt($arr['layer']);
        $self->row = Nrow::fromInt($arr['row']);
        $self->column = Ncol::fromInt($arr['column']);
        $self->roff = Roff::fromValue($arr['roff']);
        $self->coff = Coff::fromValue($arr['coff']);
        $self->itt = Itt::fromValue($arr['itt']);
        $self->timeSeriesData = TimeSeriesData::fromArray($arr['time_series_data']);
        return $self;
    }

    private function __construct()
    {
        $this->tomulth = Tomulth::fromFloat(1);
        $this->obsname = Obsname::fromString('HOBS');
        $this->layer = Nlay::fromInt(0);
        $this->row = Nrow::fromInt(0);
        $this->column = Ncol::fromInt(0);
        $this->roff = Roff::fromValue(0);
        $this->coff = Coff::fromValue(0);
        $this->itt = Itt::fromValue(1);
        $this->timeSeriesData = TimeSeriesData::fromArray([[0.0, 0.0]]);
    }

    public function toArray(): array
    {
        return [
            'tomulth' => $this->tomulth->toFloat(),
            'obsname' => $this->obsname->toString(),
            'layer' => $this->layer->toInt(),
            'row' => $this->row->toInt(),
            'column' => $this->column->toInt(),
            'irefsp' => null,
            'roff' => $this->roff->toFloat(),
            'coff' => $this->coff->toFloat(),
            'itt' => $this->itt->toInt(),
            'time_series_data' => $this->timeSeriesData->toArray()
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
