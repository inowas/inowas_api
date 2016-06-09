<?php

namespace AppBundle\Model\ModflowProcess;

use JMS\Serializer\Annotation as JMS;

/**
 * Class AbstractModflowResultProcess
 * @package AppBundle\Model\Modflow
 */
abstract class AbstractModflowResultProcess extends AbstractModflowProcess
{
    /**
     * @var bool
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $calculation = false;

    /**
     * @var bool
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $result = true;
}