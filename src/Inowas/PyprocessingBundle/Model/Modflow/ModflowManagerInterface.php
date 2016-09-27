<?php

namespace Inowas\PyprocessingBundle\Model\Modflow;

interface ModflowManagerInterface
{
    public function create();

    public function findById($id);
}