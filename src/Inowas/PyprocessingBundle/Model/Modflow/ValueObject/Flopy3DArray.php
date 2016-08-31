<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\ValueObject;

use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;

class Flopy3DArray extends FlopyArray implements FlopyArrayInterface, \JsonSerializable
{
    /**
     * @var int|float|array
     */
    private $value;

    /** @var  int */
    private $nx;

    /** @var  int */
    private $ny;

    /** @var  int */
    private $nz;

    /**
     * @param $value
     * @param $nz
     * @param $ny
     * @param $nx
     * @return Flopy3DArray
     */
    public static function fromNumeric($value, $nz, $ny, $nx){
        $instance = new self();

        if (! is_numeric($value)){
            throw new InvalidArgumentException(sprintf(
                'Value is supposed to be an integer value, %s given',
                gettype($value))
            );
        }

        $instance->nx = $nx;
        $instance->ny = $ny;
        $instance->nz = $nz;
        $instance->value = $value;
        return $instance;
    }

    /**
     * @param $value
     * @param $ny
     * @param $nx
     * @return Flopy3DArray
     */
    public static function from1DArray($value, $ny, $nx){
        $instance = new self();

        if ($instance->count_dimension($value) !== 1){
            throw new InvalidArgumentException(sprintf(
                'Value is supposed to be a 1D-array value. Value with %s Dimensions given.',
                $instance->count_dimension($value))
            );
        }

        foreach ($value as $item) {
            if (! is_numeric($item)){
                throw new InvalidArgumentException(sprintf(
                    'Value is supposed to be an integer value, %s given',
                    gettype($value))
                );
            }
        }

        $instance->nx = $nx;
        $instance->ny = $ny;
        $instance->nz = count($value);
        $instance->value = $value;
        return $instance;
    }

    /**
     * @param $value
     * @param $nx
     * @return Flopy3DArray
     */
    public static function from2DArray($value, $nx){

        $instance = new self();

        if ($instance->count_dimension($value) !== 2){
            throw new InvalidArgumentException(sprintf(
                'Value is supposed to be a 2D-array value. Value with %s Dimensions given.',
                $instance->count_dimension($value))
            );
        }

        foreach ($value as $row) {
            foreach ($row as $col) {
                if (! is_numeric($col)){
                    throw new InvalidArgumentException(sprintf(
                        'Value is supposed to be an integer value, %s given', gettype($value))
                    );
                }
            }
        }

        $instance->nx = $nx;
        $instance->ny = count($value[0]);
        $instance->nz = count($value);
        $instance->value = $value;
        return $instance;
    }

    /**
     * @param $value
     * @return Flopy3DArray
     */
    public static function from3DArray($value){

        $instance = new self();

        if ($instance->count_dimension($value) !== 3){
            throw new InvalidArgumentException(sprintf(
                'Value is supposed to be a 2D-array value. Value with %s Dimensions given.',
                $instance->count_dimension($value))
            );
        }

        foreach ($value as $layer) {
            foreach ($layer as $row) {
                foreach ($row as $col) {
                    if (! is_numeric($col)){
                        throw new InvalidArgumentException(sprintf(
                            'Value is supposed to be an integer value, %s given',
                            gettype($value))
                        );
                    }
                }
            }
        }

        $instance->nx = count($value[0][0]);
        $instance->ny = count($value[0]);
        $instance->nz = count($value);
        $instance->value = $value;
        return $instance;
    }

    /**
     * @param $value
     * @param $nLay
     * @param $nRow
     * @param $nCol
     * @return Flopy3DArray
     */
    public static function fromValue($value, $nLay=1, $nRow=1, $nCol=1)
    {

        $instance = new self();

        if ($instance->count_dimension($value) == 0){
            return $instance->fromNumeric($value, $nLay, $nRow, $nCol);
        }

        if ($instance->count_dimension($value) == 1){
            return $instance->from1DArray($value, $nRow, $nCol);
        }

        if ($instance->count_dimension($value) == 2){
            return $instance->from2DArray($value, $nCol);
        }

        if ($instance->count_dimension($value) == 3){
            return $instance->from3DArray($value);
        }

        throw new InvalidArgumentException(sprintf(
            'Value is supposed to have max. 3 Dimensions. Value with %s Dimensions given.',
            $instance->count_dimension($value))
        );
    }



    /**
     * @return array|float|int
     */
    public function toReducedArray(){
        if ($this->count_dimension($this->value) == 3){
            foreach ($this->value as $lKey => $layer){
                foreach ($layer as $rKey => $row){
                    $this->value[$lKey][$rKey] = $this->reduceArray($row);
                }

                $this->value[$lKey] = $this->reduceArray($this->value[$lKey]);
            }

            $this->value = $this->reduceArray($this->value);

            return $this->value;
        }

        if ($this->count_dimension($this->value) == 2){
            foreach ($this->value as $lKey => $layer){
                $this->value[$lKey] = $this->reduceArray($this->value[$lKey]);
            }
            $this->value = $this->reduceArray($this->value);
            return $this->value;
        }

        if ($this->count_dimension($this->value) == 1){
            return $this->reduceArray($this->value);
        }

        if ($this->count_dimension($this->value) == 0){
            return $this->value;
        }

        throw new InvalidArgumentException('The object-value is neither scalar nor 1/2 dimensional array-value.');
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        if ($this->count_dimension($this->value) == 3){
            return $this->value;
        }

        if ($this->count_dimension($this->value) == 2){
            foreach ($this->value as $lKey => $lValue){
                foreach ($lValue as $rKey => $rValue){
                    $this->value[$lKey][$rKey] = array_pad(array(), $this->nx, $rValue);
                }
            }

            return $this->value;
        }

        if ($this->count_dimension($this->value) == 1){

            foreach ($this->value as $key => $value){
                $this->value[$key] = array();
                for ($iy=0; $iy<$this->ny; $iy++){
                    $this->value[$key][$iy] = array_pad(array(), $this->nx, $value);
                }
            }

            return $this->value;
        }

        if ($this->count_dimension($this->value) == 0){
            $value = array();

            for ($iz=0; $iz<$this->nz; $iz++){
                $value[$iz] = array();
                for ($iy=0; $iy<$this->ny; $iy++){
                    $row = array_pad(array(), $this->nx, $this->value);
                    $value[$iz][] = $row;
                }
            }
            return $value;
        }

        throw new InvalidArgumentException('The object-value is neither scalar nor 1/2 dimensional array-value.');
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return $this->toReducedArray();
    }
}