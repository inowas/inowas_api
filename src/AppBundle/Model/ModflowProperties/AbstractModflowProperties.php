<?php

namespace AppBundle\Model\ModflowProperties;

use JMS\Serializer\Annotation as JMS;

/**
 * Class AbstractModflowProcess
 * @package AppBundle\Model\Modflow
 */
abstract class AbstractModflowProperties
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