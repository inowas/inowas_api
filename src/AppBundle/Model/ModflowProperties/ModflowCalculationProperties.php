<?php

namespace AppBundle\Model\ModflowProperties;

use JMS\Serializer\Annotation as JMS;

/**
 * Class ModflowCalculationProcess
 * @package AppBundle\Model\Modflow
 */
class ModflowCalculationProperties extends AbstractModflowProperties
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