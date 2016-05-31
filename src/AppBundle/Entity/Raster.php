<?php

namespace AppBundle\Entity;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * Raster
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RasterRepository")
 * @ORM\Table(name="rasters")
 */
class Raster
{
    /**
     * @var uuid
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true)
     * @JMS\Type("string")
     * @JMS\Groups({"modeldetails", "modelobjectdetails"})
     */
    private $id;

    /**
     * @var GridSize
     *
     * @ORM\Column(name="grid_size", type="grid_size", nullable=true)
     * @JMS\Groups({"modeldetails", "modelobjectdetails"})
     */
    private $gridSize;

    /**
     * @var BoundingBox
     *
     * @ORM\Column(name="bounding_box", type="bounding_box", nullable=true)
     */
    private $boundingBox;

    /**
     * @var integer
     *
     * @ORM\Column(name="srid", type="integer", nullable=true)
     * @JMS\Type("integer")
     */
    private $srid;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_data_val", type="integer")
     * @JMS\Type("integer")
     */
    private $noDataVal = -999;

    /**
     * @var integer
     *
     * @ORM\Column(name="data", type="json_array")
     * @JMS\Type("array")
     */
    private $data;

    public function __construct() {
        $this->id = Uuid::uuid4();
    }

    /**
     * Set id
     *
     * @param uuid $id
     *
     * @return Raster
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return GridSize
     */
    public function getGridSize()
    {
        return $this->gridSize;
    }

    /**
     * @param GridSize $gridSize
     * @return Raster
     */
    public function setGridSize($gridSize)
    {
        $this->gridSize = $gridSize;
        return $this;
    }

    /**
     * @return BoundingBox
     */
    public function getBoundingBox()
    {
        return $this->boundingBox;
    }

    /**
     * @param BoundingBox $boundingBox
     * @return Raster
     */
    public function setBoundingBox($boundingBox)
    {
        $this->boundingBox = $boundingBox;
        return $this;
    }


    /**
     * Set srid
     *
     * @param integer $srid
     *
     * @return Raster
     */
    public function setSrid($srid)
    {
        $this->srid = $srid;

        return $this;
    }

    /**
     * Get srid
     *
     * @return integer
     */
    public function getSrid()
    {
        return $this->srid;
    }

    /**
     * Set values
     *
     * @param array $data
     *
     * @return Raster
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set noDataVal
     *
     * @param integer $noDataVal
     *
     * @return Raster
     */
    public function setNoDataVal($noDataVal)
    {
        $this->noDataVal = $noDataVal;

        return $this;
    }

    /**
     * Get noDataVal
     *
     * @return integer
     */
    public function getNoDataVal()
    {
        return $this->noDataVal;
    }
}
