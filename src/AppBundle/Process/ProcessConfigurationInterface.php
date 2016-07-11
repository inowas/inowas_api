<?php

namespace AppBundle\Process;

interface ProcessConfigurationInterface extends PythonProcessConfigurationInterface
{
    /**
     * InterpolationProcessConfigurationInterface constructor.
     * @param ConfigurationFileCreatorInterface $configurationFileCreator
     */
    public function __construct(ConfigurationFileCreatorInterface $configurationFileCreator);

    /**
     * @return string
     */
    public function getInputFile();

    /**
     * @return string
     */
    public function getOutputFile();
    
}