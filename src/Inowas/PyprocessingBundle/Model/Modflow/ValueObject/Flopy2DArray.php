<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\ValueObject;

use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;

class Flopy2DArray extends FlopyArray implements FlopyArrayInterface
{
    /**
     * @var int|float|array
     */
    private $value;

    /** @var  int */
    private $nx;

    /** @var  int */
    private $ny;

    /**
     * @param $value
     * @param $nx
     * @param $ny
     * @return Flopy2DArray
     */
    public static function fromNumeric($value, $ny, $nx){
        $instance = new self();

        if (! is_numeric($value)){
            throw new InvalidArgumentException(sprintf('Value is supposed to be an integer value, %s given', gettype($value)));
        }

        $instance->nx = $nx;
        $instance->ny = $ny;
        $instance->value = $value;
        return $instance;
    }

    /**
     * @param $value
     * @param $nx
     * @return Flopy2DArray
     */
    public static function from1DArray($value, $nx){
        $instance = new self();

        if ($instance->count_dimension($value) !== 1){
            throw new InvalidArgumentException(sprintf('Value is supposed to be a 1D-array value. Value with %s Dimensions given.', $instance->count_dimension($value)));
        }

        foreach ($value as $item) {
            if (! is_numeric($item)){
                throw new InvalidArgumentException(sprintf('Value is supposed to be an integer value, %s given', gettype($value)));
            }
        }

        $instance->nx = $nx;
        $instance->ny = count($value);
        $instance->value = $value;
        return $instance;
    }

    /**
     * @param $value
     * @return Flopy2DArray
     */
    public static function from2DArray($value){

        $instance = new self();

        if ($instance->count_dimension($value) !== 2){
            throw new InvalidArgumentException(sprintf('Value is supposed to be a 2D-array value. Value with %s Dimensions given.', $instance->count_dimension($value)));
        }

        foreach ($value as $row) {
            foreach ($row as $col) {
                if (! is_numeric($col)){
                    throw new InvalidArgumentException(sprintf('Value is supposed to be an integer value, %s given', gettype($value)));
                }
            }
        }

        $instance->nx = count($value[0]);
        $instance->ny = count($value);
        $instance->value = $value;
        return $instance;
    }

    /**
     * @param $value
     * @param $nRow
     * @param $nCol
     * @return Flopy2DArray
     */
    public static function fromValue($value, $nRow, $nCol)
    {
        $instance = new self();

        if ($instance->count_dimension($value) == 0) {
            return $instance->fromNumeric($value, $nRow, $nCol);
        }

        if ($instance->count_dimension($value) == 1) {
            return $instance->from1DArray($value, $nRow);
        }

        if ($instance->count_dimension($value) == 2) {
            return $instance->from2DArray($value);
        }

        throw new InvalidArgumentException(sprintf('Value is supposed to have max. 2 Dimensions. Value with %s Dimensions given.', $instance->count_dimension($value)));
    }

    /**
     * @return array|float|int
     */
    public function toReducedArray(){

        if ($this->count_dimension($this->value) <= 1){
            return $this->value;
        }

        if ($this->count_dimension($this->value) == 2){
            foreach ($this->value as $key => $row){
                if (is_array($row)){
                    if (count(array_unique($row)) == 1){
                        $this->value[$key] = array_unique($row)[0];
                    }
                }
            }

            return $this->value;
        }

        throw new InvalidArgumentException('The object-value is neither scalar nor 1/2 dimensional array-value.');
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        if ($this->count_dimension($this->value) == 2){
            return $this->value;
        }

        if ($this->count_dimension($this->value) == 1){
            foreach ($this->value as $key => $value){
                $row = array_pad(array(), $this->nx, $value);
                $this->value[$key] = $row;
            }

            return $this->value;
        }

        if ($this->count_dimension($this->value) == 0){
            $value = array();
            for ($i=0; $i<$this->ny; $i++){
                $row = array_pad(array(), $this->nx, $this->value);
                $value[] = $row;
            }
            return $value;
        }

        throw new InvalidArgumentException('The object-value is neither scalar nor 1/2 dimensional array-value.');
    }
}