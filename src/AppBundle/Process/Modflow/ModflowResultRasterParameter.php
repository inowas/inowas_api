<?php

namespace AppBundle\Process\Modflow;


use AppBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\ModflowParameterInterface;

class ModflowResultRasterParameter implements ModflowParameterInterface
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
    protected $outputType = 'raster';

    /** @var  string */
    protected $modelId;

    /** @var integer */
    protected $layer;

    /** @var array */
    protected $timesteps;

    /** @var array */
    protected $stressPeriods;

    /** @var string */
    protected $operation;

    /**
     * ModflowResultRasterParameter constructor.
     * @param $modelId
     * @param $layer
     * @param array $timesteps
     * @param array $stressPeriods
     * @param $operation
     */
    public function __construct($modelId, $layer, array $timesteps, array $stressPeriods, $operation)
    {
        if (!in_array($operation, $this->availableOperations)) {
            throw new InvalidArgumentException(sprintf('The operation %s is not supported.', $operation));
        }

        $this->modelId = $modelId;
        $this->layer = $layer;
        $this->timesteps = $timesteps;
        $this->stressPeriods = $stressPeriods;
        $this->operation = $operation;
    }

    /**
     * @return mixed
     */
    function jsonSerialize()
    {
        return array(
            "model_id" => $this->modelId,
            "time_steps" => $this->timesteps,
            "stress_periods" => array(0),
            "layer" => $this->layer,
            "operation" => $this->operation,
            "output_type" => $this->outputType
        );
    }
}