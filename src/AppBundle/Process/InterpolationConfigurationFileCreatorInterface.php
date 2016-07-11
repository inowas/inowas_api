<?php

namespace AppBundle\Process;

interface InterpolationConfigurationFileCreatorInterface extends ConfigurationFileCreatorInterface
{
    public function createFiles($algorithm, InterpolationParameter $interpolationParameter);
}