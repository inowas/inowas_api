<?php

namespace AppBundle\Process;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class PythonProcess
{

    /** @var array $arguments */
    protected $arguments;

    /** @var bool */
    protected $ignoreWarnings;

    /** @var  string $prefix */
    protected $prefix;

    /** @var  string $scriptName */
    protected $scriptName;

    /** @var string $workingDirectory */
    protected $workingDirectory;

    public function __construct(PythonProcessConfigurationInterface $configuration)
    {
        $this->arguments = $configuration->getArguments();
        $this->ignoreWarnings = $configuration->getIgnoreWarnings();
        $this->prefix = $configuration->getPrefix();
        $this->scriptName = $configuration->getScriptName();
        $this->workingDirectory = $configuration->getWorkingDirectory();
    }

    /**
     * @return Process
     */
    public function getProcess()
    {
        $processBuilder = new ProcessBuilder();
        $processBuilder->setPrefix($this->prefix);
        $processBuilder->setWorkingDirectory($this->workingDirectory);

        if ($this->ignoreWarnings){
            $processBuilder->add('-W');
            $processBuilder->add('ignore');
        }

        $processBuilder->add($this->scriptName);

        foreach ($this->arguments as $argument){
            $processBuilder->add($argument);
        }
        
        return $processBuilder->getProcess();
    }

}