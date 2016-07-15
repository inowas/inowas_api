<?php

namespace Inowas\PythonProcessBundle\Model;

use AppBundle\Process\ProcessWithInputOutputFileInterface;
use AppBundle\Process\PythonProcessConfigurationInterface;
use Symfony\Component\Process\ProcessBuilder;

class PythonProcessFactory
{
    /**
     * @var \Inowas\PythonProcessBundle\Service\PythonProcess
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