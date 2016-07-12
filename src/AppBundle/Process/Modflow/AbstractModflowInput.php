<?php

namespace AppBundle\Process\Modflow;

/**
 * Class AbstractModflowProcess
 * @package AppBundle\Model\Modflow
 */
abstract class AbstractModflowInput
{
    /** @var string */
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