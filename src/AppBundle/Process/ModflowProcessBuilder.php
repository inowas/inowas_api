<?php

namespace AppBundle\Process;

class ModflowProcessBuilder extends PythonProcessBuilder
{
    public function __construct(ModflowProcessConfigurationInterface $configuration)
    {
        parent::__construct($configuration);
    }
}