<?php

namespace AppBundle\Model\ModflowProperties;

use JMS\Serializer\Annotation as JMS;

/**
 * Class AbstractModflowResultProcess
 * @package AppBundle\Model\Modflow
 */
abstract class AbstractModflowResultProperties extends AbstractModflowProperties
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