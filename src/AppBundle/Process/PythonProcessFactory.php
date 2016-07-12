<?php

namespace AppBundle\Process;

use Symfony\Component\Process\ProcessBuilder;

class PythonProcessFactory
{
    /**
     * @var \AppBundle\Service\PythonProcess
     */
    protected $pythonProcess;

    /**
     * @var ProcessWithInputOutputFileInterface
     */
    protected $configuration;

    private function __construct(){}

    public static function create(PythonProcessConfigurationInterface $configuration){
        return new PythonProcess(new ProcessBuilder(), $configuration);
    }
}