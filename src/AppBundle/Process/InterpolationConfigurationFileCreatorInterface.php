<?php

namespace AppBundle\Process;

interface InterpolationConfigurationFileCreatorInterface extends InputOutputFileInterface
{
    public function createFiles($algorithm, InterpolationParameter $interpolationParameter);
}