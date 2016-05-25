<?php

namespace AppBundle\Model\Interpolation;

class GridSize
{
    protected $nX;

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
     * @param int $nY
     * @return GridSize
     */
    public function setNY($nY)
    {
        $this->nY = $nY;
        return $this;
    }
}