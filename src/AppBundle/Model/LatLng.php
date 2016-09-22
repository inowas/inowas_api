<?php

namespace AppBundle\Model;

use AppBundle\Exception\InvalidArgumentException;

class LatLng
{
    /** @var  float */
    private $lat;

    /** @var  float */
    private $lng;

    private function __construct(){}

    public static function fromJson($json){

        $latLng = json_decode($json);

        if (!isset($latLng->lat)){
            throw new InvalidArgumentException('Given parameter has no property lat.');
        }

        if (!isset($latLng->lng)){
            throw new InvalidArgumentException('Given parameter has no property lng.');
        }

        $instance = new self();
        $instance->lat = (float)$latLng->lat;
        $instance->lng = (float)$latLng->lng;

        return $instance;
    }

    /**
     * @return float
     */
    public function getLat(): float
    {
        return $this->lat;
    }

    /**
     * @return float
     */
    public function getLng(): float
    {
        return $this->lng;
    }
}