<?php

namespace Inowas\Soilmodel\Model;

use Inowas\Soilmodel\Exception\InvalidArgumentException;

class PropertyType
{
    const K_X = 'kx';
    const K_Y = 'ky';
    const K_Z = 'kz';
    const SPECIFIC_STORAGE = 'ss';
    const SPECIFIC_YIELD = 'sy';
    const TOP_ELEVATION = 'top';
    const BOTTOM_ELEVATION = 'btm';
    const HYDRAULIC_CONDUCTIVITY = 'hc';
    const HORIZONTAL_ANISOTROPY = 'ha';
    const VERTICAL_ANISOTROPY = 'va';
    const VERTICAL_CONDUCTANCE = 'vc';
    const HYDRAULIC_HEAD = 'hh';

    private $availableTypes = array(
        'kx',
        'ky',
        'kz',
        'ss',
        'sy',
        'top',
        'btm',
        'hc',
        'ha',
        'va',
        'vc',
        'hh'
    );

    /** @var string */
    private $type;

    final private function __construct(){}

    public static function fromString(string $type){
        $instance = new self;
        if (! in_array($type, $instance->availableTypes)){
            throw new InvalidArgumentException(sprintf('The given PropertyType is not in the list of available propertyTypes.', $type));
        }

        $instance->type = $type;
        return $instance;
    }

    /**
     * @return string
     */
    public function getType(){
        return $this->type;
    }
}