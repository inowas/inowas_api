<?php

namespace AppBundle\Process;

interface InputOutputFileInterface
{
    public function getInputFile();

    public function getOutputFile();
}