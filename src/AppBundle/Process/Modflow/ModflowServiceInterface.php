<?php

namespace AppBundle\Process\Modflow;


interface ModflowServiceInterface
{

    public function calculate($modelId);

    public function getRasterResult($modelId, $layer, $timeStep);

    public function getTimeSeriesResult($modelId, $layer, $row, $column);

}