<?php

namespace AppBundle\Model\ModflowProperties;

use JMS\Serializer\Annotation as JMS;

/**
 * Class ModflowTimeSeriesResultProcess
 * @package AppBundle\Model\ModflowProcess
 */
class ModflowTimeSeriesResultProperties extends AbstractModflowResultProperties
{
    /**
     * @var string
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $outputType = 'time_series';

    /**
     * @var integer
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $layer;

    /**
     * @var integer
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $cellY;

    /**
     * @var integer
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $cellX;

    /**
     * @var array
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $timesteps;

    /**
     * ModflowTimeSeriesResultProcess constructor.
     * @param $modelId
     * @param int $layer
     * @param int $row
     * @param int $column
     */
    public function __construct($modelId, $layer = 0, $row = 0, $column = 0)
    {
        parent::__construct($modelId);
        $this->layer = $layer;
        $this->cellY = $row;
        $this->cellX = $column;
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
     * @param int $layer
     */
    public function setLayer($layer)
    {
        $this->layer = $layer;
    }

    /**
     * @return int
     */
    public function getCellY()
    {
        return $this->cellY;
    }

    /**
     * @param int $cellY
     */
    public function setCellY($cellY)
    {
        $this->cellY = $cellY;
    }

    /**
     * @return int
     */
    public function getCellX()
    {
        return $this->cellX;
    }

    /**
     * @param int $cellX
     */
    public function setCellX($cellX)
    {
        $this->cellX = $cellX;
    }

    /**
     * @return array
     */
    public function getTimesteps()
    {
        return $this->timesteps;
    }

    /**
     * @param array $timesteps
     */
    public function setTimesteps($timesteps)
    {
        $this->timesteps = $timesteps;
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

}