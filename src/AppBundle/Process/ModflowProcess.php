<?php

namespace AppBundle\Process;

class ModflowProcess extends PythonProcess
{
    public function __construct(ModflowConfigurationInterface $configuration)
    {
        parent::__construct($configuration);
    }
}