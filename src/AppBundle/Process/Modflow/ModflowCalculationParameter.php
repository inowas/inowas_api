<?php

namespace AppBundle\Process\Modflow;


class ModflowCalculationParameter implements ModflowCalculationParameterInterface
{

    /** @var  string */
    protected $modelId;

    /** @var  string */
    protected $baseUrl;

    /**
     * ModflowCalculationParameter constructor.
     * @param $modelId
     * @param $baseUrl
     */
    public function __construct($modelId, $baseUrl)
    {
        $this->modelId = $modelId;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return string
     */
    public function getModelId()
    {
        return $this->modelId;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
}