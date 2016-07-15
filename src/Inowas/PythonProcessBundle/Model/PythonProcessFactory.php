<?php

namespace Inowas\PythonProcessBundle\Model;

use Inowas\PythonProcessBundle\Model\ProcessWithInputOutputFileInterface;
use Inowas\PythonProcessBundle\Model\PythonProcessConfigurationInterface;
use Symfony\Component\Process\ProcessBuilder;

class PythonProcessFactory
{
    /**
     * @var ProcessWithInputOutputFileInterface
     */
    protected $configuration;

    private function __construct(){}

    public static function create(PythonProcessConfigurationInterface $configuration){
        return new PythonProcess(new ProcessBuilder(), $configuration);
    }
}