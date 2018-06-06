<?php
/**
 * coff : float
 *      Fractional offset from center of cell in X direction (between columns).
 *      Default is 0.
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class TimeSeriesData
{
    /** @var array */
    private $timeSeriesData;

    public static function fromArray(array $timeSeriesData = [[0., 0.]]): TimeSeriesData
    {
        return new self($timeSeriesData);
    }

    private function __construct($timeSeriesData)
    {
        $this->timeSeriesData = $timeSeriesData;
    }

    public function toArray(): array
    {
        return $this->timeSeriesData;
    }
}
