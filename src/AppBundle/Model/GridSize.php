<?php

namespace AppBundle\Model;

use JMS\Serializer\Annotation as JMS;

/**
 * Class GridSize
 * @package AppBundle\Model\Interpolation
 */
class GridSize implements \JsonSerializable
{
    /**
     * @var int
     * @JMS\Groups({"modeldetails", "modelobjectdetails", "interpolation", "rasterdetails", "modeldetails", "geoimage", "modelProperties"})
     */
    protected $nX;

    /**
     * @var int
     * @JMS\Groups({"modeldetails", "modelobjectdetails", "interpolation", "rasterdetails", "modeldetails", "geoimage", "modelProperties"})
     */
    protected $nY;

    public function __construct($nX = 0, $nY = 0)
    {
        $this->nX = $nX;
        $this->nY = $nY;
    }

    /**
     * @return int
     */
    public function getNX()
    {
        return $this->nX;
    }

    /**
     * @return int
     */
    public function getNumberOfColumns(){
        return $this->getNX();
    }

    /**
     * @param int $nX
     * @return GridSize
     */
    public function setNX($nX)
    {
        $this->nX = $nX;
        return $this;
    }

    /**
     * @return int
     */
    public function getNY()
    {
        return $this->nY;
    }

    /**
     * @return int
     */
    public function getNumberOfRows(){
        return $this->getNY();
    }

    /**
     * @param int $nY
     * @return GridSize
     */
    public function setNY($nY)
    {
        $this->nY = $nY;
        return $this;
    }

    /**
     * @return mixed
     */
    function jsonSerialize()
    {
        return array(
            'n_x' => $this->getNX(),
            'n_y' => $this->getNY()
        );
    }


}