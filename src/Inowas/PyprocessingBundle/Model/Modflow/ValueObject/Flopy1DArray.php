<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\ValueObject;

use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;

class Flopy1DArray extends FlopyArray implements FlopyArrayInterface
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var int
     */
    private $length;

    /**
     * @param $value
     * @param $length
     * @return Flopy1DArray
     */
    public static function fromNumeric($value, $length){
        $instance = new self();

        if (! is_numeric($value)){
            throw new InvalidArgumentException(sprintf('Value is supposed to be an integer value, %s given', gettype($value)));
        }

        $instance->length = $length;
        $instance->value = $value;
        return $instance;
    }

    /**
     * @param $value
     * @param $length
     * @return Flopy1DArray
     */
    public static function fromBool($value, $length){
        $instance = new self();

        if (! is_bool($value)){
            throw new InvalidArgumentException(sprintf('Value is supposed to be an integer value, %s given', gettype($value)));
        }

        $instance->length = $length;
        $instance->value = $value;
        return $instance;
    }

    /**
     * @param $valueArray
     * @return Flopy1DArray
     */
    public static function fromArray($valueArray){
        $instance = new self();

        if ($instance->countDimension($valueArray) !== 1){
            throw new InvalidArgumentException(sprintf('Value is supposed to be a 1D-array value. Value with %s Dimensions given.', $instance->countDimension($valueArray)));
        }

        $instance->length = count($valueArray);
        $instance->value = $valueArray;
        return $instance;
    }

    /**
     * @param $value
     * @param $nCol
     * @return Flopy1DArray
     */
    public static function fromValue($value, $nCol = 0){
        $instance = new self();

        if ($instance->countDimension($value) == 0){
            return $instance->fromNumeric($value, $nCol);
        }

        if ($instance->countDimension($value) == 1){
            return $instance->fromArray($value);
        }

        throw new InvalidArgumentException(sprintf(
            'Value is supposed to be a 0D or 1D-array value. Value with %s Dimensions given.',
            $instance->countDimension($value))
        );
    }

    /**
     * @return array|float|int
     */
    public function toReducedArray(){

        $value = $this->value;

        if (is_bool($value)){
            return $value;
        }

        if (is_numeric($value)){
            return $value;
        }

        if (is_array($value)){
            if (count(array_unique($value)) == 1){
                return array_unique($value)[0];
            }

            return $value;
        }

        throw new InvalidArgumentException('The object-value is neither scalar not array-value.');
    }

    /**
     * @return array
     */
    public function toArray(){

        $value = $this->value;

        if (is_bool($value)){
            return array_pad(array(), $this->length, $value);
        }

        if (is_numeric($value)){
            return array_pad(array(), $this->length, $value);
        }

        return $value;
    }
}
