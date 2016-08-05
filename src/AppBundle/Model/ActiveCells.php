<?php

namespace AppBundle\Model;

use JMS\Serializer\Annotation as JMS;

/**
 * Class ActiveCells
 * @package AppBundle\Model
 *
 * @JMS\ExclusionPolicy("none")
 */
class ActiveCells
{
    /**
     * @var
     * @JMS\Groups({"modelProperties"})
     */
    private $cells;

    private final function __construct(){}

    public static function fromArray(array $cells)
    {
        $instance = new self();
        
        if (!is_array($cells[0])){
            throw new \InvalidArgumentException(sprintf(
                'ActiveCells is supposed to be an two dimensional array, %s given',
                gettype($cells)
            ));
        }

        $instance->cells = $cells;
        return $instance;
    }

    public function toArray()
    {
        return $this->cells;
    }
}