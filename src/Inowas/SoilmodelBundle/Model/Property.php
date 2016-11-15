<?php

namespace Inowas\Soilmodel\Model;

use Ramsey\Uuid\Uuid;

class Property
{
    /** @var Uuid */
    private $id;

    /** @var PropertyType */
    private $type;

    /** @var  PropertyValue */
    private $value;

    public function __construct(PropertyType $type, PropertyValue $value)
    {
        $this->id = Uuid::uuid4();
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return PropertyType
     */
    public function getType(): PropertyType
    {
        return $this->type;
    }

    /**
     * @return PropertyValue
     */
    public function getValue(): PropertyValue
    {
        return $this->value;
    }
}
