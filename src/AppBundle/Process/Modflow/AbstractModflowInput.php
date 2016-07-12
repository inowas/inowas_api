<?php

namespace AppBundle\Process\Modflow;

use JMS\Serializer\Annotation as JMS;

/**
 * Class AbstractModflowProcess
 * @package AppBundle\Model\Modflow
 */
abstract class AbstractModflowInput
{
    /**
     * @var string
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $modelId;

    /**
     * AbstractModflowProcess constructor.
     * @param $modelId
     */
    public function __construct($modelId)
    {
        $this->modelId = $modelId;
    }
}