<?php

namespace AppBundle\Model\ModflowProperties;

use JMS\Serializer\Annotation as JMS;

/**
 * Class ModflowRasterResultProcess
 * @package AppBundle\Model\Modflow
 */
class ModflowRasterResultProperties extends AbstractModflowResultProperties
{
    const OP_DELTA = 'delta';
    const OP_MAX = 'max';
    const OP_MEAN = 'mean';
    const OP_MIN = 'min';
    const OP_RAW = 'raw';
    const OP_STANDARD_DEVIATION = 'standard_deviation';

    /**
     * @var string
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $outputType = 'raster';

    /**
     * @var integer
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $layer;

    /**
     * @var array
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $timesteps;

    /**
     * @var string
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $operation;

    /**
     * ModflowRasterResultProcess constructor.
     * @param $modelId
     * @param int $layer
     * @param string $operation
     */
    public function __construct($modelId, $layer = 0, $operation = self::OP_RAW)
    {
        parent::__construct($modelId);
        $this->layer = $layer;
        $this->operation = $operation;
        $this->timesteps = array();
    }

    /**
     * @return int
     */
    public function getLayer()
    {
        return $this->layer;
    }

    /**
     * @return array
     */
    public function getTimesteps()
    {
        return $this->timesteps;
    }

    /**
     * @param $timestep
     * @return $this
     */
    public function addTimestep($timestep)
    {
        $this->timesteps[] = $timestep;
        return $this;
    }

    /**
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }
}