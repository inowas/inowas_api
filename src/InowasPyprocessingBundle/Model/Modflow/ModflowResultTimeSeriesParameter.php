<?php

namespace InowasPyprocessingBundle\Model\Modflow;

use InowasPyprocessingBundle\Exception\InvalidArgumentException;

/**
 * Class ModflowTimeSeriesResultProcess
 * @package AppBundle\Model\ModflowProcess
 */
class ModflowResultTimeSeriesParameter implements ModflowParameterInterface
{

    const OP_DELTA = 'delta';
    const OP_MAX = 'max';
    const OP_MEAN = 'mean';
    const OP_MIN = 'min';
    const OP_RAW = 'raw';
    const OP_STANDARD_DEVIATION = 'standard_deviation';

    protected $availableOperations = array(
        self::OP_DELTA, self::OP_MAX, self::OP_MEAN, self::OP_MIN, self::OP_RAW, self::OP_STANDARD_DEVIATION
    );

    /** @var string */
    protected $modelId;

    /** @var string */
    protected $outputType = 'time_series';

    /** @var integer */
    protected $layer;

    /** @var integer */
    protected $row;

    /** @var integer */
    protected $column;

    /** @var array */
    protected $timesteps = array(1);

    /** @var array */
    protected $stressPeriods = array(0);

    /** @var  string */
    protected $operation;

    /**
     * ModflowResultTimeSeriesInput constructor.
     * @param $modelId
     * @param $layer
     * @param $row
     * @param $column
     * @param $operation
     */
    public function __construct($modelId, $layer, $row, $column, $operation)
    {
        if (!in_array($operation, $this->availableOperations)) {
            throw new InvalidArgumentException(sprintf('The operation %s is not supported.', $operation));
        }

        $this->modelId = $modelId;
        $this->layer = $layer;
        $this->row = $row;
        $this->column = $column;
        $this->operation = $operation;
    }

    function jsonSerialize()
    {
        return array(
            'model_id' => $this->modelId,
            'time_steps' => $this->timesteps,
            'stress_periods' => $this->stressPeriods,
            'layer' => $this->layer,
            'operation' => $this->operation,
            'output_type' => $this->outputType,
            'cell_x' => $this->column,
            'cell_y' => $this->row,
        );
    }
}