<?php

namespace AppBundle\Model;

use AppBundle\Entity\AbstractValue;
use AppBundle\Entity\AddBoundaryEvent;
use AppBundle\Entity\BoundaryModelObject;
use AppBundle\Entity\ChangeBoundaryEvent;
use AppBundle\Entity\ChangeLayerValueEvent;
use AppBundle\Entity\GeologicalLayer;

class EventFactory
{
    private final function __construct(){}

    /**
     * @param BoundaryModelObject $boundary
     * @return AddBoundaryEvent
     */
    public static function createAddBoundaryEvent(BoundaryModelObject $boundary)
    {
        return new AddBoundaryEvent($boundary);
    }

    /**
     * @param BoundaryModelObject $origin
     * @param BoundaryModelObject $newBoundary
     * @return ChangeBoundaryEvent
     */
    public static function createChangeBoundaryEvent(BoundaryModelObject $origin, BoundaryModelObject $newBoundary)
    {
        return new ChangeBoundaryEvent($origin, $newBoundary);
    }

    /**
     * @param GeologicalLayer $layer
     * @param PropertyType $propertyType
     * @param AbstractValue $value
     * @return ChangeLayerValueEvent
     */
    public static function createChangeLayerValueEvent(GeologicalLayer $layer, PropertyType $propertyType, AbstractValue $value)
    {
        return new ChangeLayerValueEvent($layer, $propertyType, $value);
    }
}