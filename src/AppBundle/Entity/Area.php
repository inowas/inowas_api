<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @ORM\Table(name="areas")
 * @ORM\HasLifecycleCallbacks()
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
     * @var array
     *
     * @JMS\Type("array")
     */
    private $rings;

    /**
     * @var AreaType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AreaType")
     * @JMS\Groups({"list", "details", "modelobjectdetails"})
     */
    private $areaType;

    /**
     * @return Polygon
     */
    public function getGeometry()
    {
        return $this->geometry;
    }

    /**
     * @param Polygon $geometry
     */
    public function setGeometry(Polygon $geometry)
    {
        $this->geometry = $geometry;
        $this->rings = $geometry->toArray();
    }

    /**
     * Set areaType
     *
     * @param \AppBundle\Entity\AreaType $areaType
     * @return Area
     */
    public function setAreaType(AreaType $areaType = null)
    {
        $this->areaType = $areaType;

        return $this;
    }

    /**
     * Get areaType
     *
     * @return \AppBundle\Entity\AreaType 
     */
    public function getAreaType()
    {
        return $this->areaType;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("geometry")
     * @JMS\Groups({"modelobjectdetails"})
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
}
