<?php

namespace AppBundle\Entity;

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
     * @JMS\Groups("modeldetails")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="number_of_rows", type="integer")
     * @JMS\Type("integer")
     */
    private $numberOfRows;

    /**
     * @var integer
     *
     * @ORM\Column(name="number_of_columns", type="integer")
     * @JMS\Type("integer")
     */
    private $numberOfColumns;

    /**
     * @var float
     *
     * @ORM\Column(name="upper_left_x", type="float")
     * @JMS\Type("float")
     */
    private $upperLeftX;

    /**
     * @var float
     *
     * @ORM\Column(name="upper_left_y", type="float")
     * @JMS\Type("float")
     */
    private $upperLeftY;

    /**
     * @var float
     *
     * @ORM\Column(name="lower_right_x", type="float")
     * @JMS\Type("float")
     */
    private $lowerRightX;

    /**
     * @var float
     *
     * @ORM\Column(name="lower_right_y", type="float")
     * @JMS\Type("float")
     */
    private $lowerRightY;

    /**
     * @var integer
     *
     * @ORM\Column(name="srid", type="integer")
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
     * Set numberOfRows
     *
     * @param integer $numberOfRows
     *
     * @return Raster
     */
    public function setNumberOfRows($numberOfRows)
    {
        $this->numberOfRows = $numberOfRows;

        return $this;
    }

    /**
     * Get numberOfRows
     *
     * @return integer
     */
    public function getNumberOfRows()
    {
        return $this->numberOfRows;
    }

    /**
     * Set numberOfColumns
     *
     * @param integer $numberOfColumns
     *
     * @return Raster
     */
    public function setNumberOfColumns($numberOfColumns)
    {
        $this->numberOfColumns = $numberOfColumns;

        return $this;
    }

    /**
     * Get numberOfColumns
     *
     * @return integer
     */
    public function getNumberOfColumns()
    {
        return $this->numberOfColumns;
    }

    /**
     * Set upperLeftX
     *
     * @param float $upperLeftX
     *
     * @return Raster
     */
    public function setUpperLeftX($upperLeftX)
    {
        $this->upperLeftX = $upperLeftX;

        return $this;
    }

    /**
     * Get upperLeftX
     *
     * @return float
     */
    public function getUpperLeftX()
    {
        return $this->upperLeftX;
    }

    /**
     * Set upperLeftY
     *
     * @param float $upperLeftY
     *
     * @return Raster
     */
    public function setUpperLeftY($upperLeftY)
    {
        $this->upperLeftY = $upperLeftY;

        return $this;
    }

    /**
     * Get upperLeftY
     *
     * @return float
     */
    public function getUpperLeftY()
    {
        return $this->upperLeftY;
    }

    /**
     * Set lowerRightX
     *
     * @param float $lowerRightX
     *
     * @return Raster
     */
    public function setLowerRightX($lowerRightX)
    {
        $this->lowerRightX = $lowerRightX;

        return $this;
    }

    /**
     * Get lowerRightX
     *
     * @return float
     */
    public function getLowerRightX()
    {
        return $this->lowerRightX;
    }

    /**
     * Set lowerRightY
     *
     * @param float $lowerRightY
     *
     * @return Raster
     */
    public function setLowerRightY($lowerRightY)
    {
        $this->lowerRightY = $lowerRightY;

        return $this;
    }

    /**
     * Get lowerRightY
     *
     * @return float
     */
    public function getLowerRightY()
    {
        return $this->lowerRightY;
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
