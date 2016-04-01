<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\Common\Collections\ArrayCollection;
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
     */
    protected $type = 'area';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @JMS\Groups({"list", "modelobjectdetails"})
     */
    protected $name;

    /**
     * @var ArrayCollection Property
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Property", mappedBy="modelObject", cascade={"persist", "remove"})
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Property>")
     * @JMS\Groups({"modelobjectdetails"})
     */
    protected $properties;

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
     * @JMS\Groups({"list", "details"})
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
