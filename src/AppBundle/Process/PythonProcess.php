<?php

namespace AppBundle\Process;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class PythonProcess
{
    /** @var  Uuid */
    protected $id;

    /** @var  PythonProcessConfigurationInterface */
    protected $configuration;

    /** @var  ProcessBuilder */
    protected $processBuilder;

    /** @var  Process */
    protected $process;

    /**
     * PythonProcess constructor.
     * @param ProcessBuilder $processBuilder
     * @param PythonProcessConfigurationInterface $configuration
     */
    public function __construct(ProcessBuilder $processBuilder, PythonProcessConfigurationInterface $configuration)
    {
        $this->id = Uuid::uuid4();
        $this->configuration = $configuration;
        $this->processBuilder = $processBuilder;

        $this->processBuilder->setPrefix($this->configuration->getPrefix());
        $this->processBuilder->setWorkingDirectory($this->configuration->getWorkingDirectory());

        if ($this->configuration->getIgnoreWarnings()){
            $this->processBuilder->add('-W');
            $this->processBuilder->add('ignore');
        }

        $this->processBuilder->add($this->configuration->getScriptName());

        foreach ($this->configuration->getArguments() as $argument){
            $this->processBuilder->add($argument);
        }

        $this->process = $this->processBuilder->getProcess();
        $this->process->enableOutput();
    }

    /**
     * @return Uuid|\Ramsey\Uuid\UuidInterface
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @return string
     */
    public function getErrorOutput()
    {
        return $this->getProcess()->getErrorOutput();
    }


    public function run()
    {
        if (! $this->process->isRunning()) {
            $this->process->run();
        }
    }

    public function getOutput(){
        return $this->process->getOutput();
    }

    public function isRunning()
    {
        return $this->process->isRunning();
    }

    public function isSuccessful(){

        if (! $this->process->isSuccessful()){
            return false;
        }

        $response = json_decode($this->process->getOutput());
        if (isset($response->error)){
            return false;
        }

        return true;
    }
}