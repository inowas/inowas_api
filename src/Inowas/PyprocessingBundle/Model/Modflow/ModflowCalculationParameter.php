<?php

namespace Inowas\PyprocessingBundle\Model\Modflow;


class ModflowCalculationParameter implements ModflowParameterInterface
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

    function jsonSerialize()
    {
        return array(
            'model_id' => $this->modelId,
            'base_url' => $this->baseUrl
        );
    }
}
