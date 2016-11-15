<?php

namespace Inowas\ModflowBundle\Model\ValueObject;

interface FlopyArrayInterface
{
    /**
     * @return int|float|array
     */
    public function toReducedArray();

    /**
     * @return array
     */
    public function toArray();
}
