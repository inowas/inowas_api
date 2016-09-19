<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\ValueObject;

abstract class FlopyArray implements FlopyArrayInterface
{
    /**
     * FlopyArray constructor.
     */
    protected final function __construct(){}

    /**
     * @param $array
     * @return int
     */
    protected function count_dimension($array) {
        if(is_array($array)) {
            foreach ($array as $aKey => $aValue) {
                if (is_array($aValue)) {
                    foreach ($aValue as $bKey => $bValue) {
                        if (is_array($bValue)) {
                            return 3;
                        }
                    }
                    return 2;
                }
            }
            return 1;
        }
        return 0;
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

    /**
     * @return array|float|int|mixed
     */
    public function toSingleNumericValueOrFullArray(){

        $value = $this->toReducedArray();

        if (! is_array($value)){
            return $value;
        }

        return $this->toArray();
    }

    /**
     * @return array
     */
    abstract public function toArray();
}