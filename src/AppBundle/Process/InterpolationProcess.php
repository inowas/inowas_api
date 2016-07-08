<?php

namespace AppBundle\Process;

class InterpolationProcess
{
    /**
     * @var \AppBundle\Service\PythonProcess
     */
    protected $pythonProcess;

    public function __construct(InterpolationProcessConfigurationInterface $configuration)
    {
        $this->pythonProcess = new PythonProcess($configuration);
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