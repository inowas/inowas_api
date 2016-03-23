<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @ORM\Table(name="areas")
 */
class Area extends ModelObject
{
    /**
     * @var string
     * @JMS\Groups({"list", "details"})
     */
    protected $type = 'area';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var ArrayCollection Property
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Property", mappedBy="modelObject", cascade={"persist", "remove"})
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Property>")
     */
    protected $properties;

    /**
     * @var Polygon
     *
     * @ORM\Column(name="geometry", type="polygon", nullable=true)
     */
    private $geometry;

    /**
     * @var AreaType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AreaType")
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
}
