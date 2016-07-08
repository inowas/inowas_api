<?php

namespace AppBundle\Process;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class PythonProcessBuilder
{
    /** @var  PythonProcessConfigurationInterface */
    protected $configuration;

    public function __construct(PythonProcessConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return Process
     */
    public function getProcess()
    {
        $processBuilder = new ProcessBuilder();
        $processBuilder->setPrefix($this->configuration->getPrefix());
        $processBuilder->setWorkingDirectory($this->configuration->getWorkingDirectory());

        if ($this->configuration->getIgnoreWarnings()){
            $processBuilder->add('-W');
            $processBuilder->add('ignore');
        }

        $processBuilder->add($this->configuration->getScriptName());

        foreach ($this->configuration->getArguments() as $argument){
            $processBuilder->add($argument);
        }
        
        return $processBuilder->getProcess();
    }
}