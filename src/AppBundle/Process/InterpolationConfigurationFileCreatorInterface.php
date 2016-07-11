<?php

namespace AppBundle\Process;

interface InterpolationConfigurationFileCreatorInterface
{
    public function createFiles($algorithm, InterpolationParameter $interpolationParameter);

    public function getInputFile();

    public function getOutputFile();
}