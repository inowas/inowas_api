<?php

namespace AppBundle\Model\ModflowProcess;

use JMS\Serializer\Annotation as JMS;

/**
 * Class AbstractModflowProcess
 * @package AppBundle\Model\Modflow
 */
abstract class AbstractModflowProcess
{
    /**
     * @var string
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $modelId;

    /**
     * @return string
     */
    public function getModelId()
    {
        return $this->modelId;
    }

    /**
     * @param $modelId
     * @return $this
     */
    public function setModelId($modelId)
    {
        $this->modelId = $modelId;
        return $this;
    }

    /**
     * AbstractModflowProcess constructor.
     * @param $modelId
     */
    public function __construct($modelId)
    {
        $this->modelId = $modelId;
    }
}