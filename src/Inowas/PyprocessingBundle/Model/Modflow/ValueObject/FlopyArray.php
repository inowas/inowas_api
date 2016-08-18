<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\ValueObject;

abstract class FlopyArray implements FlopyArrayInterface
{
    /**
     * FlopyArray constructor.
     */
    protected final function __construct(){}

    /**
     * @param $Array
     * @param int $count
     * @return int
     */
    protected function count_dimension($Array, $count = 0) {
        if(is_array($Array)) {
            return $this->count_dimension(current($Array), ++$count);
        } else {
            return $count;
        }
    }

    /**
     * @param array $arr
     * @return array
     */
    protected function reduceArray(array $arr){
        if ($this->count_dimension($arr) == 1){
            if (count(array_unique($arr)) == 1){
                return array_unique($arr)[0];
            }
        }

        return $arr;
    }
}