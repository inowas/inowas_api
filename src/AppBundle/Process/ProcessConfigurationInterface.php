<?php

namespace AppBundle\Process;

interface ProcessConfigurationInterface extends PythonProcessConfigurationInterface, InputOutputFileInterface
{
    /**
     * InterpolationProcessConfigurationInterface constructor.
     * @param InputOutputFileInterface $configurationFileCreator
     */
    public function __construct(InputOutputFileInterface $configurationFileCreator);
    
}