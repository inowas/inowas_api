<?php

namespace AppBundle\Model\ModflowProcess;

use JMS\Serializer\Annotation as JMS;

/**
 * Class ModflowTimeSeriesResultProcess
 * @package AppBundle\Model\ModflowProcess
 */
class ModflowTimeSeriesResultProcess extends AbstractModflowResultProcess
{
    /**
     * @var string
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $output_type = 'time_series';

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
    protected $cell_y;

    /**
     * @var integer
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $cell_x;

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
        $this->cell_y = $row;
        $this->cell_x = $column;
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
        return $this->cell_y;
    }

    /**
     * @param int $cell_y
     */
    public function setCellY($cell_y)
    {
        $this->cell_y = $cell_y;
    }

    /**
     * @return int
     */
    public function getCellX()
    {
        return $this->cell_x;
    }

    /**
     * @param int $cell_x
     */
    public function setCellX($cell_x)
    {
        $this->cell_x = $cell_x;
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