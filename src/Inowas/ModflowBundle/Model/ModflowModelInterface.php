<?php

namespace Inowas\ModflowBundle\Model;

use Inowas\ModflowBundle\Model\Boundary\Boundary;
use Ramsey\Uuid\Uuid;

interface ModflowModelInterface {
    public function getId(): Uuid;
    public function addBoundary(Boundary $boundary): ModflowModel;
    public function removeBoundary(Boundary $boundary): ModflowModel;
}