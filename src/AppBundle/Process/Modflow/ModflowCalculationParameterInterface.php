<?php

namespace AppBundle\Process\Modflow;

interface ModflowCalculationParameterInterface
{
    public function __construct($modelId, $baseUrl);

    public function getModelId();

    public function getBaseUrl();

}