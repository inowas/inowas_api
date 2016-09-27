<?php

namespace AppBundle\Model;

use AppBundle\Entity\ModFlowModel;

interface EventInterface
{
    public function applyTo(ModFlowModel $model);
}