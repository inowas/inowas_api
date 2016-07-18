<?php

namespace Inowas\PyprocessingBundle\Model\PythonProcess;

interface ProcessWithInputOutputFileInterface extends PythonProcessConfigurationInterface, InputOutputFileInterface
{
    /**
     * InterpolationProcessConfigurationInterface constructor.
     * @param InputOutputFileInterface $configurationFileCreator
     */
    public function __construct(InputOutputFileInterface $configurationFileCreator);
    
}