<?php

namespace AppBundle\Process;

use Symfony\Component\Process\ProcessBuilder;

class InterpolationProcess
{
    /**
     * @var \AppBundle\Service\PythonProcess
     */
    protected $pythonProcess;

    public function __construct(ProcessConfigurationInterface $configuration)
    {
        $this->pythonProcess = new PythonProcess(new ProcessBuilder(), $configuration);
    }

    public function interpolate()
    {
        $this->pythonProcess->run();

        if (! $this->pythonProcess->isSuccessful()) {
            return false;
        }

        return true;
    }
}