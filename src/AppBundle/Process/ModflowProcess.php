<?php

namespace AppBundle\Process;

class ModflowProcess extends PythonProcess
{
    public function __construct(ModflowProcessConfigurationInterface $configuration)
    {
        parent::__construct($configuration);
    }
}