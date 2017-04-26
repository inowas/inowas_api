<?php
/**
 * stress_period_data : list of boundaries, or recarray of boundaries, or
 * dictionary of boundaries.
 * Each river cell is defined through definition of
 * layer (int), row (int), column (int), stage (float), cond (float),
 * rbot (float).
 * The simplest form is a dictionary with a lists of boundaries for each
 * stress period, where each list of boundaries itself is a list of
 * boundaries. Indices of the dictionary are the numbers of the stress
 * period. This gives the form of::
 *
 * stress_period_data =
 * {0: [
 * [lay, row, col, stage, cond, rbot],
 * [lay, row, col, stage, cond, rbot],
 * [lay, row, col, stage, cond, rbot]
 * ],
 * 1:  [
 * [lay, row, col, stage, cond, rbot],
 * [lay, row, col, stage, cond, rbot],
 * [lay, row, col, stage, cond, rbot]
 * ], ...
 * kper:
 * [
 * [lay, row, col, stage, cond, rbot],
 * [lay, row, col, stage, cond, rbot],
 * [lay, row, col, stage, cond, rbot]
 * ]
 * }
 *
 * Note that if the number of lists is smaller than the number of stress
 * periods, then the last list of rivers will apply until the end of the
 * simulation. Full details of all options to specify stress_period_data
 * can be found in the flopy3 boundaries Notebook in the basic
 * subdirectory of the examples directory.
 */
declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

class RivStressPeriodData extends AbstractStressPeriodData
{
    /** @var array */
    protected $data = [];

    public static function create(): RivStressPeriodData
    {
        return new self();
    }

    public static function fromArray(array $data): RivStressPeriodData
    {
        $self = new self();
        $self->data = $data;
        return $self;
    }

    public function addGridCellValue(RivStressPeriodGridCellValue $gridCellValue): RivStressPeriodData
    {
        $stressPeriod = $gridCellValue->stressPeriod();
        $layer = $gridCellValue->lay();
        $row = $gridCellValue->row();
        $column = $gridCellValue->col();
        $stage = $gridCellValue->stage();
        $cond = $gridCellValue->cond();
        $rbot = $gridCellValue->rbot();

        if (! is_array($this->data)){
            $this->data = array();
        }

        if (! array_key_exists($stressPeriod, $this->data)){
            $this->data[$stressPeriod] = array();
        }

        $this->data[$stressPeriod][] = [$layer, $row, $column, $stage, $cond, $rbot];
        return $this;
    }
}
