<?php

namespace AppBundle\Model\ModflowProcess;

use JMS\Serializer\Annotation as JMS;

/**
 * Class ModflowCalculationProcess
 * @package AppBundle\Model\Modflow
 */
class ModflowCalculationProcess extends AbstractModflowProcess
{
    /**
     * @var bool
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $calculation = true;

    /**
     * @var bool
     *
     * @JMS\Groups("modflowProcess")
     */
    protected $result = false;
}