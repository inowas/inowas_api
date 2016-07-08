<?php

namespace AppBundle\Process;

class InterpolationProcessBuilder extends PythonProcessBuilder
{
    public function __construct(InterpolationProcessConfigurationInterface $configuration)
    {
        parent::__construct($configuration);
    }
}