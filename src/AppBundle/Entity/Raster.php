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
    const DEFAULT_NO_DATA_VAL = -9999;

    /**
     * @var uuid
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true)
     * @JMS\Type("string")
     * @JMS\Groups({"modeldetails", "modelobjectdetails", "rasterdetails", "soilmodellayers"})
     */
    private $id;

    /**
     * @var GridSize
     *
     * @ORM\Column(name="grid_size", type="grid_size", nullable=true)
     * @JMS\Groups({"modeldetails", "modelobjectdetails", "rasterdetails"})
     */
    private $gridSize;

    /**
     * @var BoundingBox
     *
     * @ORM\Column(name="bounding_box", type="bounding_box", nullable=true)
     * @JMS\Groups({"rasterdetails"})
     */
    private $boundingBox;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_data_val", type="integer")
     * @JMS\Groups({"rasterdetails"})
     * @JMS\Type("integer")
     */
    private $noDataVal = self::DEFAULT_NO_DATA_VAL;

    /**
     * @var array
     *
     * @ORM\Column(name="data", type="json_array")
     * @JMS\Groups({"rasterdetails"})
     * @JMS\Type("array")
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     * @JMS\Groups({"rasterdetails"})
     * @JMS\Type("string")
     */
    private $description = '';


    public function __construct() {
        $this->id = Uuid::uuid4();
        $this->description = '';
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
     * @param $filter
     * @return array
     */
    public function getFilteredData($filter){

        if (null === $filter) {
            return $this->data;
        }
        
        $data = $this->data;
        for ($yi = 0; $yi<count($data); $yi++){
            for ($xi = 0; $xi<count($data[0]); $xi++) {
                if ($filter[$yi][$xi] == false) {
                    $data[$yi][$xi] = self::DEFAULT_NO_DATA_VAL;
                }
            }
        }

        return $data;
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

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null $description
     * @return $this
     */
    public function setDescription($description = null)
    {
        if (is_null($description)) {
            $description = "";
        }

        $this->description = $description;

        return $this;
    }


}
