<?php

namespace AppBundle\Process\Modflow;

/**
 * Class ModflowCalculationProcess
 * @package AppBundle\Model\Modflow
 */
class ModflowCalculationInput extends AbstractModflowInput implements \JsonSerializable
{
    /** @var string */
    protected $baseUrl;

    public function __construct($modelId, $baseUrl = "http://localhost")
    {
        $this->baseUrl = $baseUrl;
        parent::__construct($modelId);
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

    /**
     * @return mixed
     */
    function jsonSerialize()
    {
        return array(
            'model_id' => $this->modelId,
            'base_url' => $this->baseUrl
        );
    }
}