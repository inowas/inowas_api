<?php

namespace AppBundle\Process;

use AppBundle\Process\Modflow\ModflowCalculationParameterInterface;

interface WorkspaceInterface
{
    public function getWorkspace(ModflowCalculationParameterInterface $modflowCalculationParameter);
}