<?php

namespace Inowas\Soilmodel\Model;

use Inowas\Soilmodel\Exception\InvalidArgumentException;

class PropertyValue implements \JsonSerializable
{
    /** @var  float|array */
    private $values;

    final private function __construct(){}

    public static function fromValue($values){

        $instance = new self();

        if (! is_array($values)) {
            $instance->values = $values;
            return $instance;
        }

        $maxDepth = self::array_depth($values);
        if ($maxDepth != 2){
            throw new InvalidArgumentException(sprintf('Given array is no 2-Dimensional, %s Dimensions found.', $maxDepth));
        }

        $instance->values = $values;
        return $instance;
    }

    public function getValue(){
        return $this->values;
    }

    /**
     * @return mixed
     */
    function jsonSerialize()
    {
        return $this->values;
    }

    public static function array_depth(array $array) {
        $max_depth = 1;

        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = self::array_depth($value) + 1;

                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }
            }
        }

        return $max_depth;
  }
}






