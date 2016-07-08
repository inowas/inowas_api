<?php

namespace AppBundle\Process;

use AppBundle\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class PythonProcess
{
    /** @var  PythonProcessConfigurationInterface */
    protected $configuration;

    /** @var  Process */
    protected $process;

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

        $this->process = $processBuilder->getProcess();

        return $this->process;
    }

    public function run()
    {
        if (! $this->process instanceof Process){
            $this->process = $this->getProcess();
        }

        if (! $this->process->isRunning()) {
            $this->process->run();
        }
    }

    public function isRunning()
    {
        return $this->process->isRunning();
    }

    public function isSuccessful(){
        if (! $this->process->isSuccessful()){
            return new ProcessFailedException(sprintf('Process failed: %s', $this->process->getExitCodeText()));
        }

        $response = json_decode($this->process->getOutput());
        if (isset($response->error)){
            return false;
        }

        return true;
    }
}