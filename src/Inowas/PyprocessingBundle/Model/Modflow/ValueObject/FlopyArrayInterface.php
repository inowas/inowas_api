<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\ValueObject;


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