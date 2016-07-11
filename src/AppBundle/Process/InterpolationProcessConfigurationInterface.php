<?php

namespace AppBundle\Process;

interface InterpolationProcessConfigurationInterface extends PythonProcessConfigurationInterface
{
    /**
     * InterpolationProcessConfigurationInterface constructor.
     * @param InterpolationConfigurationFileCreatorInterface $configurationFileCreator
     */
    public function __construct(InterpolationConfigurationFileCreatorInterface $configurationFileCreator);

    /**
     * @return string
     */
    public function getInputFile();

    /**
     * @return string
     */
    public function getOutputFile();
    
}