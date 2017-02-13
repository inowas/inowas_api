<?php

namespace Inowas\Modflow\Model;

class BoundaryType
{

    const AREA = 'area';
    const WELL = 'well';

    private $type;

    public static function createAreaType(){
        return new self('area');
    }

    public static function createWellType(){
        return new self('well');
    }

    public static function fromString(string $type){
        return new self($type);
    }

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public function type(){
        return $this->type;
    }
}
