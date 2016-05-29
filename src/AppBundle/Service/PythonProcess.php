<?php

namespace AppBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class PythonProcess
{
    /** @var array $arguments */
    protected $arguments;

    /** @var  ProcessBuilder $builder */
    protected $builder;

    /** @var  string $prefix */
    protected $prefix;

    /** @var  Process $process */
    protected $process;

    /** @var string $workingDirectory */
    protected $workingDirectory;

    public function __construct($prefix)
    {
        $this->builder = new ProcessBuilder();
        $this->arguments = array();
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @return ArrayCollection
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param $arguments
     * @return $this
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
        return $this;

    }

    public function setWorkingDirectory($workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
        return $this;
    }
    
    /**
     * @return Process
     */
    public function getProcess()
    {
        return $this->process = $this->builder
                ->setPrefix('python')
                ->setArguments($this->arguments)
                ->setWorkingDirectory($this->workingDirectory)
                ->getProcess();
    }
}