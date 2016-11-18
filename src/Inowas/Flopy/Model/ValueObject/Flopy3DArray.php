<?php

namespace Inowas\FlopyBundle\Model\ValueObject;

use Inowas\ModflowBundle\Exception\InvalidArgumentException;

class Flopy3DArray extends FlopyArray implements FlopyArrayInterface, \JsonSerializable
{
    /**
     * @var int|float|array
     */
    protected $value;

    /** @var  int */
    protected  $nx;

    /** @var  int */
    protected  $ny;

    /** @var  int */
    protected  $nz;

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

        if ($instance->countDimension($value) !== 1){
            throw new InvalidArgumentException(sprintf(
                'Value is supposed to be a 1D-array value. Value with %s Dimensions given.',
                $instance->countDimension($value))
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

        if ($instance->countDimension($value) !== 2){
            throw new InvalidArgumentException(sprintf(
                'Value is supposed to be a 2D-array value. Value with %s Dimensions given.',
                $instance->countDimension($value))
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

        if ($instance->countDimension($value) !== 3){
            throw new InvalidArgumentException(sprintf(
                'Value is supposed to be a 2D-array value. Value with %s Dimensions given.',
                $instance->countDimension($value))
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

        if ($instance->countDimension($value) == 0){
            return $instance->fromNumeric($value, $nLay, $nRow, $nCol);
        }

        if ($instance->countDimension($value) == 1){
            return $instance->from1DArray($value, $nRow, $nCol);
        }

        if ($instance->countDimension($value) == 2){
            return $instance->from2DArray($value, $nCol);
        }

        if ($instance->countDimension($value) == 3){
            return $instance->from3DArray($value);
        }

        throw new InvalidArgumentException(sprintf(
            'Value is supposed to have max. 3 Dimensions. Value with %s Dimensions given.',
            $instance->countDimension($value))
        );
    }



    /**
     * @return array|float|int
     */
    public function toReducedArray(){

        $value = $this->value;

        if ($this->countDimension($value) == 3){
            foreach ($value as $lKey => $layer){
                foreach ($layer as $rKey => $row){
                    if (is_array($row)){
                        $value[$lKey][$rKey] = $this->reduceArray($row);
                    }
                }

                if (is_array($value[$lKey])) {
                    $value[$lKey] = $this->reduceArray($value[$lKey]);
                }

            }

            $value = $this->reduceArray($value);



            return $value;
        }

        if ($this->countDimension($value) == 2){
            foreach ($value as $lKey => $layer){
                $value[$lKey] = $this->reduceArray($value[$lKey]);
            }
            $value = $this->reduceArray($value);
            return $value;
        }

        if ($this->countDimension($value) == 1){
            return $this->reduceArray($value);
        }

        if ($this->countDimension($value) == 0){
            return $value;
        }

        throw new InvalidArgumentException('The object-value is neither scalar nor 1/2 dimensional array-value.');
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        $value = $this->value;

        if ($this->countDimension($value) == 3){
            foreach ($value as $lKey => $lValue){
                foreach ($lValue as $rKey => $rValue){
                    if (count($rValue) == 1){
                        $value[$lKey][$rKey] = array_pad(array(), $this->nx, $rValue[0]);
                    }
                }
            }
            return $value;
        }

        if ($this->countDimension($value) == 2){
            foreach ($value as $lKey => $lValue){
                foreach ($lValue as $rKey => $rValue){
                    $value[$lKey][$rKey] = array_pad(array(), $this->nx, $rValue);
                }
            }

            return $value;
        }

        if ($this->countDimension($value) == 1){

            foreach ($value as $key => $val){
                $value[$key] = array();
                for ($iy=0; $iy<$this->ny; $iy++){
                    $value[$key][$iy] = array_pad(array(), $this->nx, $val);
                }
            }

            return $value;
        }

        if ($this->countDimension($value) == 0){
            $val = array();

            for ($iz=0; $iz<$this->nz; $iz++){
                $val[$iz] = array();
                for ($iy=0; $iy<$this->ny; $iy++){
                    $row = array_pad(array(), $this->nx, $value);
                    $val[$iz][] = $row;
                }
            }
            return $val;
        }

        throw new InvalidArgumentException('The object-value is neither scalar nor 1/2 dimensional array-value.');
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->toSingleNumericValueOrFullArray();
    }
}
