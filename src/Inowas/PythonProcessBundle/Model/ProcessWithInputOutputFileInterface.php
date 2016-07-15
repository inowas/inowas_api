<?php

namespace Inowas\PythonProcessBundle\Model;

interface ProcessWithInputOutputFileInterface extends PythonProcessConfigurationInterface, InputOutputFileInterface
{
    /**
     * InterpolationProcessConfigurationInterface constructor.
     * @param InputOutputFileInterface $configurationFileCreator
     */
    public function __construct(InputOutputFileInterface $configurationFileCreator);
    
}