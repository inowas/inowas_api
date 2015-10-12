<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\MultiPoint;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="inowas_lake")
 */
class Lake extends ModelObject
{

    /**
     * @var Polygon
     *
     * @ORM\Column(name="geometry", type="polygon", nullable=true)
     */
    private $geometry;

    /**
     * @var MultiPoint
     *
     * @ORM\Column(name="multipoint", type="polygon", nullable=true)
     */
    private $multipoint;

    /**
     * @var $raster
     *
     * @ORM\ManyToOne(targetEntity="Raster")
     */
    private $raster;

    /**
     * @return MultiPoint
     */
    public function getMultipoint()
    {
        return $this->multipoint;
    }

    /**
     * @param MultiPoint $multipoint
     */
    public function setMultipoint($multipoint)
    {
        $this->multipoint = $multipoint;
    }

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
     * @return mixed
     */
    public function getRaster()
    {
        return $this->raster;
    }

    /**
     * @param mixed $raster
     */
    public function setRaster($raster)
    {
        $this->raster = $raster;
    }
    
}