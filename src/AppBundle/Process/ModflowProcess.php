<?php

namespace AppBundle\Process;

use Symfony\Component\Process\ProcessBuilder;

class ModflowProcess extends PythonProcess
{
    public function __construct(ModflowProcessConfigurationInterface $configuration)
    {
        parent::__construct(new ProcessBuilder(), $configuration);
    }
}