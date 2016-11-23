<?php

namespace Inowas\ModflowBundle\Model;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon;

class Area extends ModelObject
{
    /** @var Polygon */
    private $geometry;

    /**
     * @return Polygon
     */
    public function getGeometry()
    {
        return $this->geometry;
    }

    /**
     * @param Polygon $geometry
     * @return $this
     */
    public function setGeometry(Polygon $geometry)
    {
        $this->geometry = $geometry;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getJsonGeometry(){
        if (! $this->geometry instanceof Polygon){
            return null;
        }

        return $this->geometry->toJson();
    }
}