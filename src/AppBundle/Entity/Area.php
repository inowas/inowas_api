<?php

namespace AppBundle\Entity;

use AppBundle\Model\BoundingBox;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AreaRepository")
 * @ORM\Table(name="areas")
 */
class Area extends ModelObject
{
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    protected $type = 'area';

    /**
     * @var Polygon
     *
     * @ORM\Column(name="geometry", type="polygon", nullable=true)
     */
    private $geometry;

    /**
     * @var string
     *
     * @ORM\Column(name="area_type", type="string", nullable=true)
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modeldetails"})
     */
    private $areaType;

    /**
     * @var float
     *
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails", "modelobjectlist"})
     */
    private $surface;

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
     * Set areaType
     *
     * @param string
     * @return Area
     */
    public function setAreaType($areaType)
    {
        $this->areaType = $areaType;

        return $this;
    }

    /**
     * Get areaType
     *
     * @return string
     */
    public function getAreaType()
    {
        return $this->areaType;
    }

    /**
     * @return float
     */
    public function getSurface()
    {
        return $this->surface;
    }

    /**
     * @param float $surface
     * @return Area
     */
    public function setSurface($surface)
    {
        $this->surface = $surface;
        return $this;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("geometry")
     * @JMS\Groups({"modelobjectdetails", "modeldetails", "soilmodeldetails"})
     *
     * @return string
     */
    public function serializeDeserializeGeometry()
    {
        $polygons = null;

        if (!is_null($this->geometry))
        {
            $new = array();
            $polygons = $this->geometry->toArray();

            foreach ($polygons as $polygon)
            {
                $polygon["type"] = $this->geometry->getType();
                $polygon["srid"] = $this->geometry->getSrid();
                $new[] = $polygon;
            }

            unset($polygons);
            $polygons = $new;
        }
        return $polygons;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("geojson")
     * @JMS\Groups({"modelobjectdetails", "modeldetails", "soilmodeldetails"})
     *
     * @return string
     */
    public function geoJson()
    {
        return $this->geometry->toJson();
    }

    /**
     * 
     */
    public function getBoundingBox()
    {
        if (! $this->geometry instanceof Polygon) {
            return null;
        }
        
        $rings = $this->geometry->toArray();
        $points = $rings[0];

        $xMin = $points[0][0];
        $xMax = $points[0][0];
        $yMin = $points[0][1];
        $yMax = $points[0][1];

        foreach ($points as $point)
        {
            if ($point[0]<$xMin) {
                $xMin =  $point[0];
            }

            if ($point[0]>$xMax) {
                $xMax = $point[0];
            }

            if ($point[1]<$yMin) {
                $yMin = $point[1];
            }

            if ($point[1]>$yMax) {
                $yMax = $point[1];
            }
        }

        return new BoundingBox($xMin, $xMax, $yMin, $yMax, $this->geometry->getSrid());
    }
}
